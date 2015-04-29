Tiny Blog Platform
==================
While this is pretty much the whole source code of my website, the most
intersting part here the Tiny Blog Platform contained in it.

The motive of creating this Tiny Blog Platform is:
1. It should be extremely easy to deply it. Most of the settings should happen
   automatically and should otherwise depend on directly editing the source
   files.
2. Each file should represent one basic functionality, and addition/removal of
   each functionality should be as easy as adding/deleting corresponding file.

The code is in /blog/ directory. It contains
1. index.php - For displaying blogs.
2. blog.xml - Sample blog entries.
3. blog.xsd - For validation of blog.xml. You may omit this file.
4. comment.php - For displaying and submitting comments.
5. xml_read.php - Needed for parsing XML (blog.xml).

Feel free to contact me if you have any questions or see any problem.


Testing
=======
To test the whole show, you will need to create an empty database in MySQL and
modify blog.php and comment.php accordingly (you need to specify username and
password, as well as database name). If you want to have reCAPTCHA, you need
to register there and modify comment.php accordingly. That is all!
