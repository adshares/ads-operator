<?php
/**
 * Copyright (C) 2018 Adshares sp. z o.o.
 *
 * This file is part of ADS Operator
 *
 * ADS Operator is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * ADS Operator is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with ADS Operator.  If not, see <https://www.gnu.org/licenses/>
 */

namespace Adshares\AdsOperator\Template;

use Adshares\AdsOperator\Template\Exception\CannotFindTemplateException;

class TwigTemplate implements TemplateInterface
{
    private $template;

    public function __construct(\Twig_Environment $template)
    {
        $this->template = $template;
    }

    public function render(string $path, array $params): string
    {
        try {
            return $this->template->render($path, $params);
        } catch (\Twig_Error_Loader $ex) {
            throw new CannotFindTemplateException(sprintf('Unable to find template `%s`', $path));
        }
    }
}
