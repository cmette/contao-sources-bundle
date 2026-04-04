<?php

namespace Cmette\ContaoSourcesBundle\EventListener\DataContainer;

use Cmette\ContaoSourcesBundle\Models\SourcesAuthorModel;
use Cmette\ContaoSourcesBundle\Models\SourcesEntityModel;
use Contao\Config;
use Contao\ContentModel;
use Contao\Controller;
use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\DataContainer;
use Contao\Date;
use Contao\Image;
use Contao\MemberGroupModel;
use Contao\StringUtil;
use Symfony\Contracts\Translation\TranslatorInterface;

class ContentElementListener
{
    public function __construct(
        private readonly ContaoFramework $framework,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public const STR_TABLE = 'tl_content';

    #[AsCallback(table: self::STR_TABLE, target: 'list.label.label', priority: 100)]
    public function ListLabelLabelCallback(array $row, string $label, DataContainer $dc, array $labels): array|string
    {
        if ('tl_theme' !== $dc->parentTable)
        {
            $arrGridLabel = $this->generateGridLabel($row);
            $contentType = $row['type'];

            switch ($contentType) {
                case 'sources_entity':
                    if(!is_null($source = SourcesEntityModel::findById($row['sources_entity'])))
                    {
                        $image = Image::getHtml('/bundles/contaosources/img/visible-red.svg', 'Achtung! Diese Quelle ist am Frontend nicht sichtbar, weil sie auch unter &raquo;Quellen&laquo; noch depubliziert ist.', ' title=""');
                        $depublished = " $image";
                        $addition = (bool)$source->published ? '':$depublished;
                        $arrGridLabel[0] = $label . $addition;
                    }
                    break;
                default:
            }

            return $arrGridLabel;
        }

        return $this->generateContentTypeLabel($row);
    }

    /**
     * @param DataContainer $dc
     * @return array
     */
    #[AsCallback(table: self::STR_TABLE, target: 'fields.sources_entity.options')]
    public function sourcesEntityOptions(DataContainer $dc): array
    {
        $len     = 40;
        $options = [];
        $authors = '';

        $sources    = SourcesEntityModel::findAll();

        if ($sources !== null)
            foreach ($sources as $source) {

                $arrAuthors = $source->getAuthors();
                $a = [];

                foreach ($arrAuthors as $author) {
                    if($_author = SourcesAuthorModel::findById($author['id'])) {
                        $a[] = $_author->getUniqueAuthor(false);
                    }
                }
                $authors = count($a) > 0 ? implode('; ', $a) . ': ' : '';

                $title      = strlen($source->title) > $len ? substr($source->title, 0, $len) . '...' : $source->title;

                $options[$source->id] = "{$authors}{$title}";
            }

        #asort($options);

        return $options;
    }





    private function generateGridLabel(array $row): array
    {
        $type = $this->generateContentTypeLabel($row);

        $objModel = $this->framework->createInstance(ContentModel::class);
        $objModel->setRow($row);

        try {
            $preview = StringUtil::insertTagToSrc($this->framework->getAdapter(Controller::class)->getContentElement($objModel));
        } catch (\Throwable $exception) {
            $preview = '<p class="tl_error">'.StringUtil::specialchars($exception->getMessage()).'</p>';
        }

        if (!empty($row['sectionHeadline'])) {
            $sectionHeadline = StringUtil::deserialize($row['sectionHeadline'], true);

            if (!empty($sectionHeadline['value']) && !empty($sectionHeadline['unit'])) {
                $preview = '<'.$sectionHeadline['unit'].'>'.$sectionHeadline['value'].'</'.$sectionHeadline['unit'].'>'.$preview;
            }
        }

        // Strip HTML comments to check if the preview is empty
        if ('' === trim(preg_replace('/<!--(.|\s)*?-->/', '', $preview))) {
            $preview = '';
        }

        return [$type, $preview, $row['invisible'] ?? null ? 'unpublished' : 'published'];
    }

    private function generateContentTypeLabel(array $row): string
    {
        $transId = "CTE.$row[type].0";
        $label = $this->translator->trans($transId, [], 'contao_default');

        if ($transId === $label) {
            $label = $row['type'];
        }

        // Add the ID of the aliased element
        if ('alias' === $row['type']) {
            $label .= ' ID '.($row['cteAlias'] ?? 0);
        }

        // Add the headline level (see #5858)
        if ('headline' === $row['type'] && \is_array($headline = StringUtil::deserialize($row['headline']))) {
            $label .= ' ('.$headline['unit'].')';
        }

        // Show the title
        if ($row['title'] ?? null) {
            $label = $row['title'].' <span class="tl_gray">['.$label.']</span>';
        }

        // Add the protection status
        if ($row['protected'] ?? null) {
            $groupIds = StringUtil::deserialize($row['groups'], true);
            $groupNames = [];

            if (!empty($groupIds)) {
                $groupIds = array_map(intval(...), $groupIds);

                if (false !== ($pos = array_search(-1, $groupIds, true))) {
                    $groupNames[] = $this->translator->trans('MSC.guests', [], 'contao_default');
                    unset($groupIds[$pos]);
                }

                if ([] !== $groupIds && null !== ($groups = $this->framework->getAdapter(MemberGroupModel::class)->findMultipleByIds($groupIds))) {
                    $groupNames += $groups->fetchEach('name');
                }
            }

            $label = $this->framework->getAdapter(Image::class)->getHtml('protected.svg').' '.$label;
            $label .= ' <span class="tl_gray">('.$this->translator->trans('MSC.protected', [], 'contao_default').($groupNames ? ': '.implode(', ', $groupNames) : '').')</span>';
        }

        if (($row['start'] ?? null) && ($row['stop'] ?? null)) {
            $label .= ' <span class="tl_gray">('.$this->translator->trans('MSC.showFromTo', [Date::parse(Config::get('datimFormat'), $row['start']), Date::parse(Config::get('datimFormat'), $row['stop'])], 'contao_default').')</span>';
        } elseif ($row['start'] ?? null) {
            $label .= ' <span class="tl_gray">('.$this->translator->trans('MSC.showFrom', [Date::parse(Config::get('datimFormat'), $row['start'])], 'contao_default').')</span>';
        } elseif ($row['stop'] ?? null) {
            $label .= ' <span class="tl_gray">('.$this->translator->trans('MSC.showTo', [Date::parse(Config::get('datimFormat'), $row['stop'])], 'contao_default').')</span>';
        }

        return $label;
    }
}