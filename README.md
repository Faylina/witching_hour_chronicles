# Witching Hour Chronicles (functional version)

This is the functional version of my first project written in PHP featuring a witchy blog.

# ----- Vision for this website -----

Witching Hour Chronicles is a simple blog-system for witchy blog posts. It tries to capture
the mysterious essence of the night and the occult with its design and uplifting posts.
At the same time, it is meant to be intuitive and practical, while offering comforting
security.

# ----- Goals -----

The goals of this project are to practice working with PHP, an Apache server and a mySQL database 
(MariaDB) and build a blog, a common element of the internet. 
Moreover, security aspects are a primary focus in this project.

# ----- Structure -----

FILE STRUCTURE
- The root directory contains the main scripts for the homepage and the dashboard, directories for 
the database, layout, function libraries, debugging tools, log files and uploaded images.
- The CSS directory houses the stylesheets for the website and the debugging tools, as well as the
images that are required for the website itself.
- The include directory contains the function libraries used for this project, the debugging tools
and the project's configuration.

WEBSITE STRUCTURE
- The homepage and the dashboard scripts contain the HTML syntax and the display of the content, the URL 
and form processing, as well as the authentification flow. 

FLOW
- The homepage displays all blog posts and categories.
- The blog posts can be filtered by category by clicking on a category on the sidebar.
- The blog posts display the category they are in, their headline and content as well as the name 
of the author, their location and the time and date of when the post was published.
- It is possible to show all blog posts again after filtering. 
- The homepage also contains a login form. Only an author with valid credentials may log in, otherwise
the form throws an error.
- Once the user is logged in, the homepage displays links to the author's dashboard and a link to log out.
- Upon logging in successfully, the user is forwarded to the author's dashboard.
- The dashboard is not accessible for a not authenticated user.
- The dashboard contains links back to the homepage and to log out. Upon logging out, the user is 
redirected back to the homepage.
- The author is greeted by name on the dashboard.
- A new blog post can be submitted here. The form contains a list of existing categories to choose from.
A headline and the content of the blog post are required and an error is thrown if they are missing. It 
is optional to upload an image. The specifications for the image are listed, otherwise an error is thrown. 
The author can choose on which side of the blog post the image will be displayed.
- Also, the author can create a new category using the provided form in one of the sidebars. An error is 
thrown if the category already exists.
- Choosing the option in one of the sidebars, an existing blog post can be viewed by choosing its headline.
For that, the form to submit a new blog post is replaced by the view of the chosen blog post. The author can
return to writing a new blog post or use the sidebar to manipulate existing posts. 
- By choosing the option in the sidebar, the author can also edit an existing post. This is only possible,
if the user is also the author of the chosen post, otherwise a message will pop up. 
- The form to submit a new blog post is replaced by the edit form that is pre-filled with the existing post's
data. If the user chooses to replace the existing image with another, the old image is also deleted from the
server. 
- By choosing the option in the sidebar, the user can also delete an existing post. This is only possible,
if the user is also the author of the chosen post, otherwise a message will pop up. The author has to confirm
this decision before the post is deleted from the database, along with its image on the server. 
- The input of all forms is sanitized and validated before further processing.
- The passwords that are stored in the database are encrypted.


DEBUGGING TOOLS
- Different functions for debugging have been used in this project to better illustrate the program flow 
and help find potential errors.
- The output of those functions can be toggled on and off by setting the DEBUG constant in the config file 
to either True or False, so the user won’t have to see the output when the game is released out of the 
development stage. 
