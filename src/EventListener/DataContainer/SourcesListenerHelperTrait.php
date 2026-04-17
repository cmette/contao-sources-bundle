<?php

namespace Cmette\ContaoSourcesBundle\EventListener\DataContainer;

use Contao\CoreBundle\DataContainer\DataContainerOperation;
use Contao\Model;

trait SourcesListenerHelperTrait
{
    /**
     * @param array $row
     * @param string $label
     * @return array|string
     */
    public function buildLabelWithCounter(array $row, string $label): array|string
    {
        $model = Model::getClassFromTable(self::STR_TABLE)::findById($row['id']);

        return ($model && (int)($count = $model->countUsage() > 0)) ?
            "<span class='used'>$label ($count)</span>" :
            "<span class='unused'>$label</span>";
    }

    /**
     * @param DataContainerOperation $operation
     * @return void
     */
    public function handleDeleteButton(DataContainerOperation $operation): void
    {
        $class = Model::getClassFromTable(self::STR_TABLE);
        $author = $class::findById($operation->getRecord()['id']);

        // Show the icon only but no link if the user cannot edit
        #if (!$this->authorizationChecker->isGranted('contao_user.example.can_edit', $operation->getRecord()['id'])) {
        if (!is_null($author) && 0 !== $author->countUsage()) {
            $operation['label'] = $GLOBALS['TL_LANG'][self::STR_TABLE]['deletion_disabled'];
            $operation['title'] = $GLOBALS['TL_LANG'][self::STR_TABLE]['deletion_disabled'];
            $operation['icon'] = 'delete--disabled.svg';
            return;
        }
    }
}