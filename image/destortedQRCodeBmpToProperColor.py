import cv2
t = cv2.imread('destortedQRCode')
t1 = cv2.cvtColor(t, cv2.COLOR_BGR2GRAY)
w,h= t1.shape
for i in range(w):
	for j in range(h):
		# print t1[i,j],"  ",(t1[i,j]==[81,179,52]).all()
		if (t1[i,j]==130 or t1[i,j]==211):
			t1[i,j] = 255
		else:
			t1[i,j] = 0

cv2.imshow("df",t1)
cv2.waitKey(0)
cv2.destroyAllWindows()


# 223