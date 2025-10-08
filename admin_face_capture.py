import cv2
import os

def register_admin(admin_name):
    # Create folder for the admin
    folder_path = f"face_data/{admin_name}"
    os.makedirs(folder_path, exist_ok=True)

    cap = cv2.VideoCapture(0)
    face_cascade = cv2.CascadeClassifier(cv2.data.haarcascades + 'haarcascade_frontalface_default.xml')

    count = 0
    print("Press 'q' to quit capturing.")
    while True:
        ret, frame = cap.read()
        gray = cv2.cvtColor(frame, cv2.COLOR_BGR2GRAY)
        faces = face_cascade.detectMultiScale(gray, 1.3, 5)

        for (x,y,w,h) in faces:
            face_img = gray[y:y+h, x:x+w]
            face_img = cv2.resize(face_img, (200, 200))
            cv2.imwrite(f"{folder_path}/{admin_name}_{count}.jpg", face_img)
            count += 1
            cv2.rectangle(frame, (x,y), (x+w, y+h), (0,255,0), 2)
        
        cv2.imshow("Register Admin Face", frame)
        if cv2.waitKey(1) & 0xFF == ord('q') or count >= 30:
            break

    cap.release()
    cv2.destroyAllWindows()
    print(f"Face registration completed for {admin_name}!")

if __name__ == "__main__":
    admin_name = input("Enter Admin Name: ")
    register_admin(admin_name)
