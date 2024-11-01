<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit78e2c0d166bdb9f5f87828d901e60fd5
{
    public static $files = array (
        '0e6d7bf4a5811bfa5cf40c5ccd6fae6a' => __DIR__ . '/..' . '/symfony/polyfill-mbstring/bootstrap.php',
        '5255c38a0faeba867671b61dfda6d864' => __DIR__ . '/..' . '/paragonie/random_compat/lib/random.php',
        '72579e7bd17821bb1321b87411366eae' => __DIR__ . '/..' . '/illuminate/support/helpers.php',
    );

    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Symfony\\Polyfill\\Mbstring\\' => 26,
            'Symfony\\Contracts\\Translation\\' => 30,
            'Symfony\\Component\\Translation\\' => 30,
            'Svg\\' => 4,
            'Stripe\\' => 7,
        ),
        'R' => 
        array (
            'RedQ\\Payment\\' => 13,
        ),
        'I' => 
        array (
            'Illuminate\\Support\\' => 19,
            'Illuminate\\Contracts\\' => 21,
        ),
        'F' => 
        array (
            'FontLib\\' => 8,
        ),
        'D' => 
        array (
            'Dompdf\\' => 7,
        ),
        'B' => 
        array (
            'Braintree\\' => 10,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Symfony\\Polyfill\\Mbstring\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-mbstring',
        ),
        'Symfony\\Contracts\\Translation\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/translation-contracts',
        ),
        'Symfony\\Component\\Translation\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/translation',
        ),
        'Svg\\' => 
        array (
            0 => __DIR__ . '/..' . '/phenx/php-svg-lib/src/Svg',
        ),
        'Stripe\\' => 
        array (
            0 => __DIR__ . '/..' . '/stripe/stripe-php/lib',
        ),
        'RedQ\\Payment\\' => 
        array (
            0 => __DIR__ . '/..' . '/redq/payment',
        ),
        'Illuminate\\Support\\' => 
        array (
            0 => __DIR__ . '/..' . '/illuminate/support',
        ),
        'Illuminate\\Contracts\\' => 
        array (
            0 => __DIR__ . '/..' . '/illuminate/contracts',
        ),
        'FontLib\\' => 
        array (
            0 => __DIR__ . '/..' . '/phenx/php-font-lib/src/FontLib',
        ),
        'Dompdf\\' => 
        array (
            0 => __DIR__ . '/..' . '/dompdf/dompdf/src',
        ),
        'Braintree\\' => 
        array (
            0 => __DIR__ . '/..' . '/braintree/braintree_php/lib/Braintree',
        ),
    );

    public static $fallbackDirsPsr4 = array (
        0 => __DIR__ . '/..' . '/nesbot/carbon/src',
    );

    public static $prefixesPsr0 = array (
        'U' => 
        array (
            'UpdateHelper\\' => 
            array (
                0 => __DIR__ . '/..' . '/kylekatarnls/update-helper/src',
            ),
        ),
        'S' => 
        array (
            'Sabberworm\\CSS' => 
            array (
                0 => __DIR__ . '/..' . '/sabberworm/php-css-parser/lib',
            ),
        ),
        'D' => 
        array (
            'Doctrine\\Common\\Inflector\\' => 
            array (
                0 => __DIR__ . '/..' . '/doctrine/inflector/lib',
            ),
        ),
        'B' => 
        array (
            'Braintree' => 
            array (
                0 => __DIR__ . '/..' . '/braintree/braintree_php/lib',
            ),
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'Dompdf\\Cpdf' => __DIR__ . '/..' . '/dompdf/dompdf/lib/Cpdf.php',
        'HTML5_Data' => __DIR__ . '/..' . '/dompdf/dompdf/lib/html5lib/Data.php',
        'HTML5_InputStream' => __DIR__ . '/..' . '/dompdf/dompdf/lib/html5lib/InputStream.php',
        'HTML5_Parser' => __DIR__ . '/..' . '/dompdf/dompdf/lib/html5lib/Parser.php',
        'HTML5_Tokenizer' => __DIR__ . '/..' . '/dompdf/dompdf/lib/html5lib/Tokenizer.php',
        'HTML5_TreeBuilder' => __DIR__ . '/..' . '/dompdf/dompdf/lib/html5lib/TreeBuilder.php',
        'UserplaceGateway' => __DIR__ . '/../..' . '/includes/userplace-payment-utility.php',
        'Userplace\\AdminColumn' => __DIR__ . '/../..' . '/includes/class-userplace-admin-column.php',
        'Userplace\\Admin_Column_Builder' => __DIR__ . '/../..' . '/includes/generator/class-admin-column.php',
        'Userplace\\Admin_Lacalize' => __DIR__ . '/../..' . '/includes/class-userplace-admin-localize.php',
        'Userplace\\Admin_Menu' => __DIR__ . '/../..' . '/includes/class-userplace-admin-menu.php',
        'Userplace\\Admin_Scripts' => __DIR__ . '/../..' . '/includes/class-userplace-admin-scripts.php',
        'Userplace\\Ajax_Handler' => __DIR__ . '/../..' . '/includes/class-userplace-ajax-handler.php',
        'Userplace\\CommentSinglePostRestriction' => __DIR__ . '/../..' . '/includes/class-userplace-comment-single-post-restrictions.php',
        'Userplace\\CustomTables' => __DIR__ . '/../..' . '/includes/userplace-custom-tables.php',
        'Userplace\\ExtendTinyMC' => __DIR__ . '/../..' . '/includes/class-tinymc-shortcode-button.php',
        'Userplace\\Generate_MetaBox' => __DIR__ . '/../..' . '/includes/generator/class-userplace-metabox.php',
        'Userplace\\Generate_Metabox_Saver' => __DIR__ . '/../..' . '/includes/generator/class-userplace-save-metabox.php',
        'Userplace\\Generate_Post_Type' => __DIR__ . '/../..' . '/includes/generator/class-userplace-post-type.php',
        'Userplace\\GoogleMapLoading' => __DIR__ . '/../..' . '/includes/class-google-map.php',
        'Userplace\\ICONS_Provider' => __DIR__ . '/../..' . '/includes/class-userplace-icons-provider.php',
        'Userplace\\Install' => __DIR__ . '/../..' . '/includes/class-userplace-install.php',
        'Userplace\\Integrations' => __DIR__ . '/../..' . '/includes/class-lister-integration.php',
        'Userplace\\Listing' => __DIR__ . '/../..' . '/includes/class-userplace-listing.php',
        'Userplace\\LoginRegister' => __DIR__ . '/../..' . '/includes/class-userplace-login-register.php',
        'Userplace\\PDFInvoice' => __DIR__ . '/../..' . '/includes/payments/class-pdf-invoice.php',
        'Userplace\\PayAsUGo' => __DIR__ . '/../..' . '/includes/payments/class-redq-payasugo.php',
        'Userplace\\Payment_Frontend_Scripts' => __DIR__ . '/../..' . '/includes/class-userplace-frontend-scripts.php',
        'Userplace\\Payment_Info' => __DIR__ . '/../..' . '/includes/payments/class-redq-payments-info.php',
        'Userplace\\Payment_Init' => __DIR__ . '/../..' . '/includes/payments/class-redqfw-payment-init.php',
        'Userplace\\Payment_Shortcode' => __DIR__ . '/../..' . '/includes/class-userplace-shortcode.php',
        'Userplace\\Post_Restrictions' => __DIR__ . '/../..' . '/includes/class-userplace-post-restrictions.php',
        'Userplace\\Provider' => __DIR__ . '/../..' . '/includes/class-userplace-provider.php',
        'Userplace\\Reuse_Builder' => __DIR__ . '/../..' . '/includes/class-userplace-reuse-from.php',
        'Userplace\\Router' => __DIR__ . '/../..' . '/includes/class-userplace-router.php',
        'Userplace\\SaveMeta' => __DIR__ . '/../..' . '/includes/class-userplace-save-meta.php',
        'Userplace\\Template_Loader' => __DIR__ . '/../..' . '/includes/class-userplace-template-loader.php',
        'Userplace\\Userplace_Feedback_message' => __DIR__ . '/../..' . '/includes/class-userplace-feedback-message.php',
        'Userplace\\ViewHelper' => __DIR__ . '/../..' . '/includes/userplace-view-restriction-helper.php',
        'Userplace_Mobile_Nav_Walker' => __DIR__ . '/../..' . '/includes/class-mobile-menu-walker.php',
        'Userplace_Nav_Walker' => __DIR__ . '/../..' . '/includes/class-frontend-menu-walker.php',
        'Userplace_Walker_Nav_Menu_Edit_Custom' => __DIR__ . '/../..' . '/includes/class-userplace-nav-menu-metabox.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit78e2c0d166bdb9f5f87828d901e60fd5::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit78e2c0d166bdb9f5f87828d901e60fd5::$prefixDirsPsr4;
            $loader->fallbackDirsPsr4 = ComposerStaticInit78e2c0d166bdb9f5f87828d901e60fd5::$fallbackDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit78e2c0d166bdb9f5f87828d901e60fd5::$prefixesPsr0;
            $loader->classMap = ComposerStaticInit78e2c0d166bdb9f5f87828d901e60fd5::$classMap;

        }, null, ClassLoader::class);
    }
}