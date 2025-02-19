How Make Database Work in Our Project 
==========================
Download:
[VScode](https://code.visualstudio.com/) , 
[php 8.4 ](https://windows.php.net/download#php-8.4) , 
[Xampp 8.2.12](https://www.apachefriends.org/download.html)

This project is the result of the effort of a group of Najran University students, graduates of the year 2025. The system aims to transform the appointment booking process from the traditional manual system to an integrated automatic system.

VScode
-------

1- Download the latest version [VScode](https://code.visualstudio.com/)

![vscode](https://i.ibb.co/F4p11xbP/Screenshot-2025-02-19-005704.png)

2- install

![vscode](https://i.ibb.co/wrFRbr2Z/Screenshot-2025-02-19-005753.png)
![vscode](https://i.ibb.co/Kpmv6Hnx/Screenshot-2025-02-19-010724.png)

3- Open Folder to see Codes in path 

![vscode](https://i.ibb.co/276QqY7s/Screenshot-2025-02-19-010934.png)

YOU CAN NOW EDIT CODE

[php 8.4 ](https://windows.php.net/download#php-8.4)
-------
1- Create floder name php in C, C:\

![](https://i.ibb.co/1YdwFW0N/Screenshot-2025-02-19-012023.png)

2- Search

![](https://i.ibb.co/FL83yBGc/Screenshot-2-19-2025-3-38-23-PM.png)

3- edit

![](https://i.ibb.co/JWgyRgBF/Environment-Variables-2-19-2025-3-29-32-PM.png)

4- click new then C:\php then OK

![](https://i.ibb.co/7dhN8fNn/Edit-environment-variable-2-19-2025-3-33-16-PM.png)

go to cmd enter , should apper Verion of php

```bash
$ php -v
```
if you have error install [Microsoft Visual C++](https://learn.microsoft.com/en-us/cpp/windows/latest-supported-vc-redist?view=msvc-170)


![](https://i.ibb.co/sdg6Vxn3/Screenshot-2025-02-19-160532.png)


[Xampp 8.2.12](https://www.apachefriends.org/download.html)
-----
1- If you find this message, just ignore it

![](https://i.postimg.cc/26d8cYb7/Warning-2-19-2025-4-18-31-PM.png)

2- install , click next for all defult settings

![](https://i.postimg.cc/QCKLYGMd/Setup-2-19-2025-4-23-34-PM.png)

3- click allow for network , And make like photo

![](https://i.postimg.cc/QCKLYGMd/Setup-2-19-2025-4-23-34-PM.png)

Go to Shell
enter
```
// cd YOUR PATH FOLDER PROJECT
```
then
```
// php -S loacalhost:ANYFORNUMBER
```

![](https://i.postimg.cc/Dy4VhXKp/XAMPP-for-Windows-php-S-localhost-6666-2-19-2025-4-39-48-PM.png)

then copy url and put in any browser


[phpmyadmin](http://localhost/phpmyadmin)
--------------
1- go to [http://localhost/phpmyadmin/](http://localhost/phpmyadmin/)

2- go to import 
![](https://i.postimg.cc/0r4B4mxS/Screenshot-2025-02-19-160532.png)
3- select folder project file name *Database.sql*
![](https://i.postimg.cc/kB8fhpTr/Screenshot-2025-02-19-165451.png)

go to url localhost 

Get supported Monolog and help fund the project with the [Tidelift Subscription](https://tidelift.com/subscription/pkg/packagist-paquettg-php-html-parser?utm_source=packagist-paquettg-php-html-parser&utm_medium=referral&utm_campaign=enterprise).

Tidelift delivers commercial support and maintenance for the open source dependencies you use to build your applications. Save time, reduce risk, and improve code health, while paying the maintainers of the exact dependencies you use.

Loading Files
------------------

You may also seamlessly load a file into the DOM instead of a string, which is much more convenient and is how I expect most developers will be loading the HTML. The following example is taken from our test and uses the "big.html" file found there.

```php
// Assuming you installed from Composer:
require "vendor/autoload.php";
use PHPHtmlParser\Dom;

$dom = new Dom;
$dom->loadFromFile('tests/data/big.html');
$contents = $dom->find('.content-border');
echo count($contents); // 10

foreach ($contents as $content)
{
	// get the class attr
	$class = $content->getAttribute('class');
	
	// do something with the html
	$html = $content->innerHtml;

	// or refine the find some more
	$child   = $content->firstChild();
	$sibling = $child->nextSibling();
}
```

This example loads the html from big.html, a real page found online, and gets all the content-border classes to process. It also shows a few things you can do with a node but it is not an exhaustive list of the methods that a node has available.

Loading URLs
----------------

Loading a URL is very similar to the way you would load the HTML from a file. 

```php
// Assuming you installed from Composer:
require "vendor/autoload.php";
use PHPHtmlParser\Dom;

$dom = new Dom;
$dom->loadFromUrl('http://google.com');
$html = $dom->outerHtml;

// or
$dom->loadFromUrl('http://google.com');
$html = $dom->outerHtml; // same result as the first example
```

loadFromUrl will, by default, use an implementation of the `\Psr\Http\Client\ClientInterface` to do the HTTP request and a default implementation of `\Psr\Http\Message\RequestInterface` to create the body of the request. You can easily implement your own version of either the client or request to use a custom HTTP connection when using loadFromUrl.

```php
// Assuming you installed from Composer:
require "vendor/autoload.php";
use PHPHtmlParser\Dom;
use App\Services\MyClient;

$dom = new Dom;
$dom->loadFromUrl('http://google.com', null, new MyClient());
$html = $dom->outerHtml;
```

As long as the client object implements the interface properly, it will use that object to get the content of the url.

Loading Strings
---------------

Loading a string directly is also easily done.

```php
// Assuming you installed from Composer:
require "vendor/autoload.php";
use PHPHtmlParser\Dom;

$dom = new Dom;
$dom->loadStr('<html>String</html>');
$html = $dom->outerHtml;
```

Options
-------

You can also set parsing option that will effect the behavior of the parsing engine. You can set a global option array using the `setOptions` method in the `Dom` object or a instance specific option by adding it to the `load` method as an extra (optional) parameter.

```php
// Assuming you installed from Composer:
require "vendor/autoload.php";
use PHPHtmlParser\Dom;
use PHPHtmlParser\Options;

$dom = new Dom;
$dom->setOptions(
    // this is set as the global option level.
    (new Options())
        ->setStrict(true)
);

$dom->loadFromUrl('http://google.com', 
    (new Options())->setWhitespaceTextNode(false) // only applies to this load.
);

$dom->loadFromUrl('http://gmail.com'); // will not have whitespaceTextNode set to false.
```

At the moment we support 12 options.

**Strict**

Strict, by default false, will throw a `StrickException` if it find that the html is not strictly compliant (all tags must have a closing tag, no attribute with out a value, etc.).

**whitespaceTextNode**

The whitespaceTextNode, by default true, option tells the parser to save textnodes even if the content of the node is empty (only whitespace). Setting it to false will ignore all whitespace only text node found in the document.

**enforceEncoding**

The enforceEncoding, by default null, option will enforce an character set to be used for reading the content and returning the content in that encoding. Setting it to null will trigger an attempt to figure out the encoding from within the content of the string given instead. 

**cleanupInput**

Set this to `false` to skip the entire clean up phase of the parser. If this is set to true the next 3 options will be ignored. Defaults to `true`.

**removeScripts**

Set this to `false` to skip removing the script tags from the document body. This might have adverse effects. Defaults to `true`.

**removeStyles**

Set this to `false` to skip removing of style tags from the document body. This might have adverse effects. Defaults to `true`.

**preserveLineBreaks**

Preserves Line Breaks if set to `true`. If set to `false` line breaks are cleaned up as part of the input clean up process. Defaults to `false`.

**removeDoubleSpace**

Set this to `false` if you want to preserve whitespace inside of text nodes. It is set to `true` by default.

**removeSmartyScripts**

Set this to `false` if you want to preserve smarty script found in the html content. It is set to `true` by default.

**htmlSpecialCharsDecode**

By default this is set to `false`. Setting this to `true` will apply the php function `htmlspecialchars_decode` too all attribute values and text nodes.

**selfClosing**

This option contains an array of all self closing tags. These tags must be self closing and the parser will force them to be so if you have strict turned on. You can update this list with any additional tags that can be used as a self closing tag when using strict. You can also remove tags from this array or clear it out completly.

**noSlash**

This option contains an array of all tags that can not be self closing. The list starts off as empty but you can add elements as you wish.

Static Facade
-------------

You can also mount a static facade for the Dom object.

```PHP
PHPHtmlParser\StaticDom::mount();

Dom::loadFromFile('tests/big.hmtl');
$objects = Dom::find('.content-border');

```

The above php block does the same find and load as the first example but it is done using the static facade, which supports all public methods found in the Dom object.

Modifying The Dom
-----------------

You can always modify the dom that was created from any loading method. To change the attribute of any node you can just call the `setAttribute` method.

```php
use PHPHtmlParser\Dom;

$dom = new Dom;
$dom->loadStr('<div class="all"><p>Hey bro, <a href="google.com">click here</a><br /> :)</p></div>');
$a = $dom->find('a')[0];
$a->setAttribute('class', 'foo');
echo $a->getAttribute('class'); // "foo"
```

You may also get the `PHPHtmlParser\Dom\Tag` class directly and manipulate it as you see fit.

```php
use PHPHtmlParser\Dom;

$dom = new Dom;
$dom->loadStr('<div class="all"><p>Hey bro, <a href="google.com">click here</a><br /> :)</p></div>');
/** @var Dom\Node\AbstractNode $a */
$a   = $dom->find('a')[0];
$tag = $a->getTag();
$tag->setAttribute('class', 'foo');
echo $a->getAttribute('class'); // "foo"
```

It is also possible to remove a node from the tree. Simply call the `delete` method on any node to remove it from the tree. It is important to note that you should unset the node after removing it from the `DOM``, it will still take memory as long as it is not unset.

```php
use PHPHtmlParser\Dom;

$dom = new Dom;
$dom->loadStr('<div class="all"><p>Hey bro, <a href="google.com">click here</a><br /> :)</p></div>');
/** @var Dom\Node\AbstractNode $a */
$a   = $dom->find('a')[0];
$a->delete();
unset($a);
echo $dom; // '<div class="all"><p>Hey bro, <br /> :)</p></div>');
```

You can modify the text of `TextNode` objects easily. Please note that, if you set an encoding, the new text will be encoded using the existing encoding.

```php
use PHPHtmlParser\Dom;

$dom = new Dom;
$dom->loadStr('<div class="all"><p>Hey bro, <a href="google.com">click here</a><br /> :)</p></div>');
/** @var Dom\Node\InnerNode $a */
$a   = $dom->find('a')[0];
$a->firstChild()->setText('biz baz');
echo $dom; // '<div class="all"><p>Hey bro, <a href="google.com">biz baz</a><br /> :)</p></div>'
```
