<?php namespace Pyrocms\BladeRunner;

use Illuminate\View\Engines\CompilerEngine;

class BladeRunnerCompilerEngine extends CompilerEngine
{
	protected $refresh = true;

	public function refresh($refresh = true)
	{
		$this->refresh = $refresh;

		return $this;
	}

	/**
	 * Get the evaluated contents of the view.
	 *
	 * @param  string  $path
	 * @param  array   $data
	 * @return string
	 */
	public function get($path, array $data = array())
	{
		if ($this->refresh)
		{
			$this->compiler->compile($path);
		}

		return parent::get($path, $data);
	}
}