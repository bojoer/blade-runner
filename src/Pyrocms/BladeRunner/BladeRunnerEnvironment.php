<?php namespace Pyrocms\BladeRunner;

use Illuminate\View\Environment;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\ViewFinderInterface;

class BladeRunnerEnvironment extends Environment
{
	public function make($view, $data = array(), $mergeData = array())
	{
		$view = parent::make($view, $data, $mergeData);

		$this->engines->resolve('blade')->getCompiler()->setView($view);

		return $view;
	}
}