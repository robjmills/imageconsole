# imageconsole
Simple proof of concept for manipulating an image on a remote filesystem, which is passed as an argument to a console task.

Usage:

```
php console.php image:store lebowski.jpg put
php console.php image:store lebowski-remote.jpg get
php console.php image:store lebowski.jpg destroy
```
