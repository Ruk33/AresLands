## HTMLPurifier 4.5.0 Bundle for Laravel

You must autoload the bundle in bundles.php:

    return array(
        'htmlpurifier' => array('auto' => true)
    );

### How to purify text

```php
<?php

$bad_input    = 'Hello!<script>alert("Malicious popup!! Your coding skills suck!")</script>';
$purifier     = IoC::resolve('HTMLPurifier');
$clean_output = $purifier->purify($bad_input);
echo $clean_output; 
// "Hello!"

```

## HTML Purifier

HTML Purifier is a standards-compliant HTML filter library written in PHP. HTML Purifier will not only remove all malicious code (better known as XSS) with a thoroughly audited, secure yet permissive whitelist, it will also make sure your documents are standards compliant, something only achievable with a comprehensive knowledge of W3C's specifications. 
It is released under the LGPL license.

- Homepage:      http://htmlpurifier.org/
- Documentation: http://htmlpurifier.org/docs

For full details on usage, see the documentation.