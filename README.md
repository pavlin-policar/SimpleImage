# SimpleImage
A simple image management system for resizing various images.

For a long time, I struggled with various image sizes. Thumbnails, preview images and any other nonsense resizing must be
performed when undertaking the production of any website. Although tools do exist to simplify this, I found that setting them up
would often take longer than doing the resizing manually. So I decided to try to make a very simple, easy to implement image
system, that handles all the resizing for me. With the introduciton of the HTML5 <picture> tag, the need for this is
even greater, as you need several different image sizes for each image you put on your website to keep loading times
reasonable and to maintain responsivness.

## Installation

coming soon.

## Usage
After you've correctly installed the package, there really isn't much more to do. When linking to an image on your webhost
simply append the parameters you would like to have applied. Currently, there are 3 modes of operation:

### Resize by height
Keep the aspect ratio and specify a new height.

E.g.
``` url/image.jpg/h:400px ```

### Resize by width
Keep the aspect ratio and specify a new width.

E.g.
``` url/image.jpg/w:800px ```

### Resize by width and height
This will not keep the aspect ratio but stretch the image to satisfy the parameters.

E.g.
``` url/image.jpg/w:400/h:400 ```

## Notes
You may be worried that image processing takes a long time, and under a heavy load this could become a major problem. This
system uses a cache system, where any image that has been generated will be stored on disk so every dimension you request
will be generated only once. The 'cache' is a small mysqlite database so no external database access is necessary.
