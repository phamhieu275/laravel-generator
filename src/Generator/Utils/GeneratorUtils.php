<?php namespace Bluecode\Generator\Utils;

class GeneratorUtils
{
    public static function validateFieldInput($fieldInputStr)
    {
        $fieldInputs = explode(':', $fieldInputStr);

        if (count($fieldInputs) < 2) {
            return false;
        }

        return true;
    }

    public static function processFieldInput($fieldInputStr, $type, $validations)
    {
        $fieldInputs = explode(':', $fieldInputStr);

        $fieldName = $fieldInputs[0];

        $fieldTypeOptions = explode(',', $fieldInputs[1]);
        $fieldType = $fieldTypeOptions[0];
        $fieldTypeParams = [];
        if (count($fieldTypeOptions) > 1) {
            for ($i = 1; $i < count($fieldTypeOptions); $i++) {
                $fieldTypeParams[] = $fieldTypeOptions[$i];
            }
        }

        $fieldOptions = [];
        if (count($fieldInputs) > 2) {
            $fieldOptions[] = $fieldInputs[2];
        }

        $typeOptions = explode(':', $type);
        $type = $typeOptions[0];
        if (count($typeOptions) > 1) {
            $typeOptions = $typeOptions[1];
        } else {
            $typeOptions = [];
        }

        return [
            'fieldName'       => $fieldName,
            'type'            => $type,
            'typeOptions'     => $typeOptions,
            'fieldInput'      => $fieldInputStr,
            'fieldType'       => $fieldType,
            'fieldTypeParams' => $fieldTypeParams,
            'fieldOptions'    => $fieldOptions,
            'validations'     => $validations,
        ];
    }

    public static function validateFieldsFile($fields)
    {
        $fieldsArr = [];

        foreach ($fields as $field) {
            if (!self::validateFieldInput($field['field'])) {
                throw new \RuntimeException('Invalid Input '.$field['field']);
            }

            if (isset($field['type'])) {
                $type = $field['type'];
            } else {
                $type = 'text';
            }

            if (isset($field['validations'])) {
                $validations = $field['validations'];
            } else {
                $validations = [];
            }

            $fieldsArr[] = self::processFieldInput($field['field'], $type, $validations);
        }

        return $fieldsArr;
    }

    public static function fillTemplate($variables, $template)
    {
        foreach ($variables as $variable => $value) {
            if (!is_string($value)) {
                continue;
            }

            $template = str_replace($variable, $value, $template);
        }

        return $template;
    }

    public static function createField($field)
    {
        $fieldInputs = explode(':', $field);

        $fieldName = array_shift($fieldInputs);

        $fieldTypeInputs = array_shift($fieldInputs);

        $fieldTypeInputs = explode(',', $fieldTypeInputs);

        $fieldType = array_shift($fieldTypeInputs);

        $fieldStr = "\$table->".$fieldType."('".$fieldName."'";

        if (count($fieldTypeInputs) > 0) {
            foreach ($fieldTypeInputs as $param) {
                $fieldStr .= ', '.$param;
            }
        }

        $fieldStr .= ')';

        if (count($fieldInputs) > 0) {
            foreach ($fieldInputs as $input) {
                $input = explode(',', $input);

                $option = array_shift($input);

                $fieldStr .= '->'.$option.'(';

                if (count($input) > 0) {
                    foreach ($input as $param) {
                        $fieldStr .= "'".$param."', ";
                    }

                    $fieldStr = substr($fieldStr, 0, strlen($fieldStr) - 2);
                }

                $fieldStr .= ')';
            }
        }

        $fieldStr .= ";";

        return $fieldStr;
    }
}
