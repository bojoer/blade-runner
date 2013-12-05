<?php namespace Pyrocms\BladeRunner;

use Illuminate\Foundation\AliasLoader;
use Illuminate\View\ViewServiceProvider;

class BladeRunnerViewServiceProvider extends ViewServiceProvider {

	public function boot()
	{
		$this->package('pyrocms/blade-runner');
	}

	/**
	 * Register the Blade engine implementation.
	 *
	 * @param  \Illuminate\View\Engines\EngineResolver  $resolver
	 * @return void
	 */
	public function registerBladeEngine($resolver)
	{
		$app = $this->app;

		$resolver->register('blade', function() use ($app)
		{
			$cache = $app['path.storage'].'/views';

			// The Compiler engine requires an instance of the CompilerInterface, which in
			// this case will be the Blade compiler, so we'll first create the compiler
			// instance to pass into the engine so it can compile the views properly.
			$compiler = new BladeRunnerCompiler($app['files'], $cache);

			$compiler->boot();

			return new BladeRunnerCompilerEngine($compiler, $app['files']);
		});
	}

       /**
         * Register the view environment.
         *
         * @return void
         */
        public function registerEnvironment()
        {
        		$app = $this->app;

                $app->bind('view', function($app)
                {
                        // Next we need to grab the engine resolver instance that will be used by the
                        // environment. The resolver will be used by an environment to get each of
                        // the various engine implementations such as plain PHP or Blade engine.
                        $resolver = $app['view.engine.resolver'];

                        $finder = $app['view.finder'];

                        $env = new BladeRunnerEnvironment($resolver, $finder, $app['events']);

                        // We will also set the container instance on this view environment since the
                        // view composers may be classes registered in the container, which allows
                        // for great testable, flexible composers for the application developer.
                        $env->setContainer($app);

                        $env->share('app', $app);

                        return $env;
                });
        }

}