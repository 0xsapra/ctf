# Mac Mounts basics


## List All Mounted Drives and their Partitions from the Terminal

```
$ diskutil list

/dev/disk0 (internal, physical):
   #:                       TYPE NAME                    SIZE       IDENTIFIER
   0:      GUID_partition_scheme                        *121.3 GB   disk0
   1:                        EFI EFI                     209.7 MB   disk0s1
   2:          Apple_CoreStorage Macintosh HD            120.5 GB   disk0s2
   3:                 Apple_Boot Recovery HD             650.0 MB   disk0s3

/dev/disk1 (internal, virtual):
   #:                       TYPE NAME                    SIZE       IDENTIFIER
   0:                  Apple_HFS Macintosh HD           +120.1 GB   disk1

```
so i can just read these images or partion like

`dd if=/dev/disk0s3 of=~/Desktop/asdf bs=1m skip=10000 count=40668937`


## fixing corrupted Partitions

> suppose we got a corrupted image in SD card and we need to fix it, so lets plug it in, and follow me:

* diskutil list
```
/dev/disk0 (internal, physical):
   #:                       TYPE NAME                    SIZE       IDENTIFIER
   0:      GUID_partition_scheme                        *121.3 GB   disk0
   1:                        EFI EFI                     209.7 MB   disk0s1
   2:          Apple_CoreStorage Macintosh HD            120.5 GB   disk0s2
   3:                 Apple_Boot Recovery HD             650.0 MB   disk0s3

/dev/disk1 (internal, virtual):
   #:                       TYPE NAME                    SIZE       IDENTIFIER
   0:                  Apple_HFS Macintosh HD           +120.1 GB   disk1
                                 Logical Volume on disk0s2
                                 125AEBF4-ECC4-4976-9746-B3136C9D8A22
                                 Unencrypted

/dev/disk3 (external, physical):
   #:                       TYPE NAME                    SIZE       IDENTIFIER
   0:     FDisk_partition_scheme                        *1.0 TB     disk3
   1:             Windows_FAT_32 ELEMENTS                970.0 GB     disk3s1
   1:             BootSector			                 30.0 GB      disk3s2

```


* diskutil info disk3

> so assume /dev/disk3s2 is corrupted, 

* `dd if=/dev/disk3s2 out=~/Desktop/asdf bs=1m`

> now try foremost,Binwalk, try manual mounting `mount -t iso9660 ~/Desktop/asdf /whtever`,debugfs to try finding bug, check magic number












