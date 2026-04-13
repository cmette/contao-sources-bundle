<?php

declare(strict_types=1);

/*
 * Contao Pedigree Bundle for Contao Open Source CMS
 *
 * Copyright (c) 2023 C. Mette
 *
 * @package    contao-pedigree-bundle
 * @link       https://github.com/cmette/contao-pedigree-bundle
 * @license    LGPL-3.0-or-later
 * @author     Christian Mette
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cmette\ContaoSourcesBundle\EventListener\DataContainer;

use Cmette\ContaoSourcesBundle\Models\SourcesAuthorModel;
use Contao\Controller;
use Contao\CoreBundle\Csrf\ContaoCsrfTokenManager;
use Contao\CoreBundle\DataContainer\DataContainerOperation;
use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\DataContainer;
use Contao\Image;
use Contao\Model;
use Contao\StringUtil;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class SourcesAuthorListener
{
    use SourcesListenerHelperTrait;
    private const STR_TABLE = 'tl_sources_author';

    public string $requestToken = '';

    public function __construct(
        private readonly ContaoCsrfTokenManager $tokenManager,
        #private readonly AuthorizationCheckerInterface $authorizationChecker,
    )
    {
        $this->requestToken = htmlspecialchars($this->tokenManager->getDefaultTokenValue());
    }

    #[AsCallback(table: self::STR_TABLE, target: 'list.label.group')]
    public function ListLabelGroupCallback(string $group, string|null $mode, string $field, array $row, DataContainer $dc): string
    {
        return $row['family_name'][0];
    }

    /**
     * @param array  $row   record data
     * @param string $label current label
     *
     * @return array
     */
    #[AsCallback(table: self::STR_TABLE, target: 'list.label.label')]
    public function ListLabelLabelCallback(array $row, string $label, DataContainer $dc, array $labels): array|string
    {
        $author = SourcesAuthorModel::findById($row['id']);

        return null === $author ? '?' : $author->getUniqueAuthor();
    }

    /**
     * controls the delete button depending on the use of the author
     */
    #[AsCallback(table: self::STR_TABLE, target: 'list.operations.delete.button')]
    #public function listDeleteButton(array $row, $href, $label, $title, $icon, $attributes): string
    public function listDeleteButton(DataContainerOperation $operation): void
    {
        $this->handleDeleteButton($operation);
    }
}
