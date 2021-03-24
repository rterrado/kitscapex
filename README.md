# kitscapex
An experimental PHP MVC Framework built with microservices design pattern in mind. 
To contribute, you can contact me: terradokenjie@gmail.com 
<br />
Author: Ken Terrado

## KitScape Engines
Engines can be considered as the main controllers of the KitScape framework. They serve as the backend processes which connects the View and the microservices/apps.

### Template Engine
KitScape has its own Template Engine which binds data into template expressions. Expressions are structured to be close to the syntax of the English language. At the moment, here are the list of valid expressions: 

##### Includes
`{{ include sections/helloworld.html }}`
<br/>
The include expression will include the helloworld.html which should live in the /sections folder of the theme folder. Includes can also include a file inside the /snippets folder. 

##### Imports 
`{{ @import Users using id then get userdata }}`
<br/>
The `@import` expression will import microservice/app from the /apps folder through the Import Engine. It starts with the `@import` keyword, followed by the name of the microservice/app (ex `Users`), then followed by the query data key ( parsed from the url, example: www.example.com/index.php?id=THIS_DATA ). The `then` keyword signifies that the you are expecting that the `Users`, for example, will return an array of data. The `get` tells the Template Engine to create a variable (in the example above, the variable name would be `userdata`) and store data in it. 

##### Retrievers
`{{ userdata::first_name }}`
<br/>
Retrievers serve as data-binding feature that tells the Template Engine to print out specific data. Retrievers will only work if there is an `@import` app written beforehand. 

#### Loops 
`{{ loop snippet/post-snippet using userdata::posts }}`
<br/>
Loops includes specified html file using a given array of data. The data (referenced by userdata::posts in the example above) will be passed to the html file that is looped for its consumption. 


