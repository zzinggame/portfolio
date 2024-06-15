<?php

namespace YOOtheme\Framework\Wordpress;

use YOOtheme\Framework\Routing\UrlGenerator as BaseGenerator;

class UrlGenerator extends BaseGenerator
{
    /**
     * {@inheritdoc}
     */
    public function route($pattern = '', $parameters = [], $referenceType = self::ABSOLUTE_PATH)
    {
        if ($pattern !== '') {
            $search = [];

            foreach ($parameters as $key => $value) {
                $search[] = '#:' . preg_quote($key, '#') . '(?!\w)#';
            }

            $pattern = preg_replace($search, $parameters, $pattern);
            $pattern = preg_replace('#\(/?:.+\)|\(|\)|\\\\#', '', $pattern);

            $parameters = array_merge(['p' => $pattern], $parameters);
        }

        return $this->to($this->request->getBaseRoute(), $parameters, $referenceType);
    }
}
