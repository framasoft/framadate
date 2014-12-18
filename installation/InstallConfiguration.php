<?php

class InstallConfiguration
{
    /**
     * @var array
     */
    private $datas;

    /**
     * @var array
     */
    private $checks = array(
        'title' => 'Application name',
        'email' => 'email address',
        'no-reply-email' => 'no-reply@mydomain.com',
        'db-name' => 'database name',
        'db-user' => 'database user',
        'db-pass' => 'database password',
        'db-host' => 'database server',
        'db-type' => 'database type',
    );

    /**
     * @param array     $datas
     */
    public function __construct(array $datas)
    {
        $this->datas = $datas;
    }

    /**
     * @return bool
     */
    public function checkValues()
    {
        foreach (array_keys($this->checks) as $key) {
            if (isset($this->datas[$key]) === false) {
                return false;
            }
        }

        return true;
    }

    public function copy($template, $destination)
    {
        $configuration = file_get_contents($template);
        if (false === $configuration) {
            throw new \Exception('Impossible to read template configuration');
        }

        $configuration = $this->convertConfigurationFile($configuration);

        if (file_put_contents($destination, $configuration) === false) {
            throw new \Exception('Impossible to save configuration');
        }
    }


    private function convertConfigurationFile($content)
    {
        foreach ($this->checks as $replace => $search) {
            $content = str_replace(
                '\'<'.$search.'>\'',
                var_export($this->datas[$replace], true),
                $content
            );
        }

        return $content;
    }
}
