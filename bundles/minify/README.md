Laravel JS/CSS Minifier/Combiner
================
With this bundle, the Laravel framework can minify your CSS and JS assets, and will cache it until it changes.
It uses the fabulous plugin Minify (http://code.google.com/p/minify/).

Install your bundle
    <code>php artisan bundle:install Laravel-JS-CSS-Minifier-Combiner</code>

For CSS (in your <head>):

    <link rel="stylesheet" type="text/css" href="<?=Minifier::make(array('//css/main.css'))?>">

Or for JavaScript (at the end of <body>):

    <script type="text/javascript" src="<?=Minifier::make(array('//js/jquery.css', '//js/reveal.js'))?>"></script>

Here's something you'll want to add to your bundles.php:

    'minify' => array(
        'autoloads' => array(
            'map' => array(
                'Minifier' => '(:bundle)/minifier.php',
            )
        ),
        'handles' => 'min'
    )

If your Laravel is in the Document Root (it normally shouldn't) and you're using mod_rewrite instead, 
you may have trouble getting Minify to work. Try this in your bundles/minify/libraries/min/config.php:

    $min_documentRoot = substr(__FILE__, 0, strrpos(__FILE__, 'bundles')) . 'public_html/';

PS: Don't forget to CHMOD 777 the cache folder in this bundle!
