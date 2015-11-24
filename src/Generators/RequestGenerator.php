<?php namespace Bluecode\Generator\Generators;

class RequestGenerator extends BaseGenerator implements GeneratorInterface
{
    /**
     * Get the type of command
     *
     * @return string
     */
    public function getType()
    {
        return 'request';
    }

    /**
     * Get the template path for generate
     *
     * @return string
     */
    public function getTemplatePath()
    {
        return '';
    }

    public function generate($data = [])
    {
        // create request
        $this->setTemplatePath('scaffold/requests/CreateRequest');
        $filename = 'Create'.$data['MODEL_NAME'].'Request.php';
        $this->generateFile($filename, $data);

        // update request
        $this->setTemplatePath('scaffold/requests/UpdateRequest');
        $filename = 'Update'.$data['MODEL_NAME'].'Request.php';
        $this->generateFile($filename, $data);
    }
}
