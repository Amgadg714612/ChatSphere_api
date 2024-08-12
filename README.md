تم  عمل  مشروع PHP معتمد على بنية MVC ونظام API وإضافة كافة الوظائف المطلوبة وهو API مصممة لدعم تطبيقات الدردشة والمراسلة. توفر هذه الـ API مجموعة من النقاط النهائية لإدارة المستخدمين، المجموعات، الرسائل، والمحادثات.
 يمكن استخدام هذه API في تطوير تطبيقات الدردشة الخاصة بالشركات أو للأغراض الشخصية

 OutPut  
![image](https://github.com/user-attachments/assets/f467482d-17e9-495a-9673-9a8f5776e812)


![image](https://github.com/user-attachments/assets/6e1bfb87-815d-46dc-abe8-c33c4a3a0480)


![image](https://github.com/user-attachments/assets/e8b7019e-1d13-4f7d-b265-781cfaed784f)

![image](https://github.com/user-attachments/assets/ae0f7b02-3df2-493e-8f64-28dffac91541)


![image](https://github.com/user-attachments/assets/601b5e58-63b5-4800-a0a3-56e5bfa29e59)



	http://localhost/ChatSphere/ChatSphere_api/

{"error":"Endpoint not found"}


Sign uP 
http://localhost/ChatSphere/ChatSphere_api/chat-api/signup

{
    "username": "ssssdssw",
    "email": "aaaaasssdsssa@example.com",
    "password": "QQQwea123sd"
}
![image](https://github.com/user-attachments/assets/c41b40c0-276f-46a5-908f-bb6c716ba5a8)


 ![image](https://github.com/user-attachments/assets/d3de9151-5d0c-4dae-b25c-ef605015dc0a)


Login 
http://localhost/ChatSphere/ChatSphere_api/chat-api/login
{
    "email": "aaasdsssa@example.com",
    "password": "QQQwea123sd"
}

![image](https://github.com/user-attachments/assets/160cd692-53be-41e2-9357-2b0d854eda00)


 ![image](https://github.com/user-attachments/assets/0bfcbdea-3afb-42bc-b9ae-e33275addf8e)



 يتم إرسال التوكن ليقوم الخادم بمراجعة صلاحيات المستخدم المرتبطة به، مثل صلاحيات المشرف أو المستخدم العادي 
 Authorization: Bearer <your_token_here>


create   Groups
{
    "name": "h م ",
    "description": "مجموعة خاصة  بالسيرب",
     "action":  "create Group"
}

 ![image](https://github.com/user-attachments/assets/5d381047-2fd5-47a2-a61e-dbca0f574c60)


add Member to group 
{
    "groupId": "1",
    "action":"addMember",
    "memberEmail":"amjad2024@gmail.com"

}

![image](https://github.com/user-attachments/assets/23fdced7-d338-4aec-859c-97cbf4d7c945)


send massage to  group   if public 
{
  "groupId": "1",
  "message": "hi   my is ahmahed to  file "
}
![image](https://github.com/user-attachments/assets/a4f17add-d768-4fa0-96cc-a3ffe21beb86)

send massage to   group  but replay  
{
  "groupId": "1",
  "message": "محمد  صلي الله   علية  وسلم ",
  "action": "reply",
  "replyTo":"3"
}
![image](https://github.com/user-attachments/assets/ada30b7c-4bd7-4945-a809-b34cba1ff860)


show massage in  group 1 
{
 "groupId": "1"
}
![image](https://github.com/user-attachments/assets/a3aff4b3-bcf9-4190-a174-711b1de975e3)





