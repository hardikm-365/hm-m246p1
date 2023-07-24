<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Framework\GraphQl\Schema\Type;

use Magento\Framework\GraphQl\Schema\TypeInterface;

/**
 * Interface for GraphQl WrappedType used to wrap other types like array or not null
 *
 * @api
 */
interface WrappedTypeInterface extends \GraphQL\Type\Definition\WrappingType, TypeInterface
{
}
