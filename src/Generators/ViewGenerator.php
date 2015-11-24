<?php namespace Bluecode\Generator\Generators;

class ViewGenerator extends BaseGenerator implements GeneratorInterface
{
    /**
     * Get the type of command
     *
     * @return string
     */
    public function getType()
    {
        return 'view';
    }

    /**
     * Get the template path for generate
     *
     * @return string
     */
    public function getTemplatePath()
    {
        return 'scaffold/views/';
    }

    public function getPaginatePath()
    {
        return $this->templatePath . 'paginate.blade';
    }

    public function generate($data = [])
    {
        // set path to view folder
        $this->rootPath = config('generator.path_view').$data['VIEW_FOLDER_NAME'].'/';

        $this->command->comment("\nViews created: ");
        $this->templateData = $data;
        
        $this->generateIndex();
        $this->generateForm();
        $this->generateCreate();
        $this->generateEdit();
        $this->generateShow();
    }

    private function generateIndex()
    {
        $templateData = $this->templateData;

        if ($this->command->option('paginate')) {
            $templateData['PAGINATE'] = $this->generateContent($this->getPaginatePath(), $templateData);
        } else {
            $templateData['PAGINATE'] = '';
        }

        $headerColumns = $bodyColumns = [];
        foreach ($this->fillableColumns as $column) {
            $headerColumns[] = '<th>'.title_case(str_replace('_', ' ', $column['field']))."</th>";

            $bodyColumns[] = '<td>{!! $'.$templateData['MODEL_NAME_CAMEL'].'->'.$column['field']." !!}</td>";
        }

        $templateData['FIELD_HEADER'] = implode("\n\t\t\t\t", $headerColumns);
        $templateData['FIELD_BODY'] = implode("\n\t\t\t\t\t", $bodyColumns);

        $filename = 'index.blade.php';
        $this->generateFile($filename, $templateData, $this->templatePath.'index.blade');
    }

    private function generateForm()
    {
        $fieldTemplate = $this->getTemplate($this->templatePath.'form_field.blade');

        $fields = [];
        logger($this->fillableColumns);
        foreach ($this->fillableColumns as $column) {
            switch ($column['type']) {
                case 'integer':
                    $inputType = 'number';
                    break;
                case 'text':
                    $inputType = 'textarea';
                    break;
                case 'date':
                    $inputType = $column['type'];
                    break;
                case 'boolean':
                    $inputType = 'checkbox';
                    break;
                default:
                    $inputType = 'text';
                    break;
            }

            $fields[] = $this->compile($fieldTemplate, [
                'FIELD_NAME' => $column['field'],
                'LABEL'      => title_case(str_replace('_', ' ', $column['field'])),
                'INPUT_TYPE' => $inputType
            ]);
        }

        $templateData = $this->templateData;
        $templateData['FIELDS'] = implode("\n\n", $fields);

        $filename = 'form.blade.php';
        $this->generateFile($filename, $templateData, $this->templatePath.'form.blade');
    }

    private function generateShow()
    {
        $fieldTemplate = $this->getTemplate($this->templatePath.'form_field.blade');

        $fields = [];
        foreach ($this->fillableColumns as $column) {
            $fields[] = $this->compile($fieldTemplate, [
                'FIELD_NAME' => $column['field'],
                'LABEL'      => title_case(str_replace('_', ' ', $column['field'])),
            ]);
        }

        $templateData = $this->templateData;
        $templateData['FIELDS'] = implode("\n\n", $fields);

        $filename = 'show.blade.php';
        $this->generateFile($filename, $templateData, $this->templatePath.'show.blade');
    }

    private function generateCreate()
    {
        $filename = 'create.blade.php';
        $this->generateFile($filename, $this->templateData, $this->templatePath.'create.blade');
    }

    private function generateEdit()
    {
        $filename = 'edit.blade.php';
        $this->generateFile($filename, $this->templateData, $this->templatePath.'edit.blade');
    }
}
