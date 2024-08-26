# CodeTest_2022_v6


* I have created a jobResouce which is json resource but not used it in all the methods ideally it will 
be used in all the methods 
* I created response marco called api again will be used in all the responses 

* Please note that i was not able to change the whole code still i tried to demonstrate how it could 
be improved changing the code structure a bit will help alought but i don't have a full picture 
of the code so did the changes with what i could guess should be done .

* I noted that it is spitting out everything from database to api  which will cause issue when 
we have large response size say 1 mb we can avoid this by thowing only the data neccassary in 
our case i created only one Json resouce JobResource to demonstate that it can return 
single response or we can use new JobResource::collection to return multiple + it handles 
pagination response very clean for us . keys total next prev page etc .

* I have created scoped query for the filters being reused on the jobs to avoid loop holes when using 
on multiple places and can be mainted easily

* You will see that the changes i made are much more friendly if we need to modify a feild we can do it on one place
query /response etc.

* i Created a StoreJob request which will pre validate the request before entering the repository 
* method failedValidation() returns json if the any errors are there.

* Only databse logic should be inside the repository so 
* created 3 services Email , push , sms and then Notification Serivce class 
* notfication service class uses instances of all three classes and contains all the notifications that need to be sent 
* this can further be sprated and interface can be created but need to know full picture of the code and how much types of notification are there 
* instead of service then we can create a sperate libarary and import in our project to send notifications
* all the classes are registered in app service provider

# *** IMP ***
* i have not created a working example of the code but a reflection of how the code can be refactored and simplified 

* using tools like laravel debug bar or telescope we can optimize the query proformance also 

* Most of places uses foreach which can be replaced by map function.
* Map function actually takes the same array and modify it which is more memory efficent i did one for example in booking repository 
* attributes can be used in some places to avoid foreach loops 

* the approach reduced almost 70% code from repo and moved it  events / services / request /models /providers etc.

* code can be modified it better if its has routes &  model folder also. 

* i checked namespace of the project is changed please dont mind if i used APP namespace somewhere instead of DTApi
* Added switch for example  too many if else's can be removed more if else's requires more processing power more memory consumption
