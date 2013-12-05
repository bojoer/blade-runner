<?php namespace Pyrocms\BladeRunner;

use Illuminate\View\View;

class BladeRunnerViewDataParser
{
	protected $view;

	public function __construct(View $view)
	{
		$this->view = $view;
	}

	public function getView()
	{
		return $this->view;
	}

	public function getData()
	{
		return $this->view->getData();
	}
}