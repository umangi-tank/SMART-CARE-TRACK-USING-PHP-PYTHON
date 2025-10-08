import cv2
import os
import numpy as np

def load_faces(admin_name):
    folder_path = f"face_data/{admin_name}"
    images = []
    for file in os.listdir(folder_path):
        img = cv2.imread(os.path.join(folder_path, file), cv2.IMREAD_GRAYSCALE)
        images.append(img)
    return images

def train_recognizer(admin_name):
    faces = load_faces(admin_name)
    labels = np.zeros(len(faces), dtype=int)
    recognizer = cv2.face.LBPHFaceRecognizer_create()
    recognizer.train(faces, labels)
    return recognizer

def login_admin(admin_name):
    recognizer = train_recognizer(admin_name)
    cap = cv2.VideoCapture(0)
    face_cascade = cv2.CascadeClassifier(cv2.data.haarcascades + 'haarcascade_frontalface_default.xml')

    print("Press 'q' to quit login.")
    while True:
        ret, frame = cap.read()
        gray = cv2.cvtColor(frame, cv2.COLOR_BGR2GRAY)
        faces = face_cascade.detectMultiScale(gray, 1.3, 5)

        for (x,y,w,h) in faces:
            face_img = gray[y:y+h, x:x+w]
            face_img = cv2.resize(face_img, (200,200))
            label, confidence = recognizer.predict(face_img)
            cv2.rectangle(frame, (x,y), (x+w, y+h), (0,255,0), 2)
            cv2.putText(frame, f"{admin_name} ({confidence:.0f})", (x,y-10), cv2.FONT_HERSHEY_SIMPLEX, 0.8, (0,255,0), 2)
            if confidence < 60:
                print("Admin recognized successfully!")
                cap.release()
                cv2.destroyAllWindows()
                return True
        
        cv2.imshow("Admin Face Login", frame)
        if cv2.waitKey(1) & 0xFF == ord('q'):
            break

    cap.release()
    cv2.destroyAllWindows()
    print("Admin not recognized.")
    return False

if __name__ == "__main__":
    admin_name = input("Enter Admin Name for login: ")
    login_admin(admin_name)
