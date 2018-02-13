<?php namespace Wn\Generators\Commands;


use InvalidArgumentException;

class ControllerCommand extends BaseCommand {

	protected $signature = 'wn:controller
        {model : Name of the model (with namespace if not App)}
		{--no-routes= : without routes}
        {--force= : override the existing files}
        {--path=app : where to store the model php file.}
        {--laravel : Use Laravel style route definitions}
    ';

	protected $description = 'Generates RESTful controller using the RESTActions trait';

    public function handle()
    {
    	$model = $this->argument('model');
        $path = $this->option('path');
        $fullName = "App\\" . $model;
    	if (isset($path)) {
    	    $fullName = $this->getNamespace() . "\\" . $model;
        }
        
    	$name = '';
    	if(strrpos($model, "\\") === false){
    		$name = $model;
    		$model = "App\\" . $model;
    	} else {
    		$name = explode("\\", $model);
    		$name = $name[count($name) - 1];
    	}
        $controller = ucwords(str_plural($name)) . 'Controller';
        $content = $this->getTemplate('controller')
        	->with([
        		'name' => $controller,
        		'model' => $model,
                'full_name' => $fullName
        	])
        	->get();

        $this->save($content, "./app/Http/Controllers/{$controller}.php", "{$controller}");
        if(! $this->option('no-routes')){
            $options = [
                'resource' => snake_case($name, '-'),
                '--controller' => $controller,
            ];

            if ($this->option('laravel')) {
                $options['--laravel'] = true;
            }

            $this->call('wn:route', $options);
        }
    }

    protected function getNamespace()
    {
        return str_replace(' ', '\\', ucwords(str_replace('/', ' ', $this->option('path'))));
    }

}
