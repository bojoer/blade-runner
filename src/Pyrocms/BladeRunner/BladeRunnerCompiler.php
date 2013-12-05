<?php namespace Pyrocms\BladeRunner;

use Closure;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;

class BladeRunnerCompiler extends BladeCompiler
{
	protected static $viewDataParser;

	/**
	 * All of the plugins that have been registered.
	 *
	 * @var array
	 */
	static public $plugins = array();

    public static function setView($view)
    {
    	static::$viewDataParser = static::getViewDataParser($view);
    }

    public function boot()
    {
		$this->extend(function($content) {

			return $this->parse($content);

		});

		$this->plugin('hello', function($params) {

	    	return isset($params['name']) ? $params['name'] : 'Hello World!';

	    });

	    return $this;
    }

    public static function getViewDataParser($view)
    {
    	return new BladeRunnerViewDataParser($view);
    }

	/**
	 * Register a template plugin.
	 *
	 * @param  string   $name
	 * @param  Closure  $plugin
	 * @return void
	 */  
	public static function plugin($name, Closure $plugin)
	{
		static::$plugins[$name] = $plugin;
	}

	/**
	 * Parse the content for template tags.
	 *
	 * @return string
	 */
	public static function parse($content)
	{

		print_r(static::$viewDataParser->getData()); exit;

		if(count(static::$plugins) == 0)
		{
			// The regular expression will match all Blade tags if there are
			// no plugins. To prevent this from happening, parsing will be
			// forced to end here.
			return $content;
		}

		$names = array();

		foreach(static::$plugins as $name => $plugin)
		{
			$names[] = preg_quote($name, '/');
		}

		$regexp = '/\{\{('.implode('|', $names).')(.*?)\}\}/u';

		return  preg_replace_callback($regexp, function($match)
		{
			list(, $name, $params) = $match;

			if( ! empty($params))
			{
				// The tag's parameters need to be converted into a PHP array.
				// Single quotes will need to be backslashed to prevent them
				// from accidentally escaping out.
				$params = addcslashes($params, '\'');
				$params = preg_replace('/ (.*?)="(.*?)"/', '\'$1\'=>\'$2\',', $params);
				$params = substr($params, 0, -1);
			}

			return '<?php echo '.get_called_class().'::call(\''.$name.'\', array('.$params.')); ?>';
		}, $content);
	}

	/**
	 * Call a template plugin.
	 *
	 * @param  string  $name
	 * @param  array   $params
	 * @return mixed
	 */
	public static function call($name, $params = array())
	{
		$plugin = static::$plugins[$name];

		return $plugin($params);
	}
}