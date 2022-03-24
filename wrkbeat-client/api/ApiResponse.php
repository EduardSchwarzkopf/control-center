<?php


class ApiResponse
{

    static public function CreateResponse(ResponseInterface $object)
    {

        $vars = get_object_vars($object);

        $hideFieldsList = $object->GetHiddenFields();
        foreach ($hideFieldsList as $hiddenField) {
            unset($vars[$hiddenField]);
        }

        return json_encode($vars);
    }
}
