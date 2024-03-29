<?php
/**
 * @package     Joomla.Site
 * @subpackage  Templates.beez3
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$app = JFactory::getApplication();
$templateparams = $app->getTemplate(true)->params;
$images = json_decode($this->item->images);
$urls = json_decode($this->item->urls);
$user    = JFactory::getUser();
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');
JHtml::_('behavior.caption');

// Create shortcut to parameters.
$params = $this->item->params;

?>
<article class="item-page<?php echo $this->pageclass_sfx?>">
<?php if ($this->params->get('show_page_heading')) : ?>

<?php if ($this->params->get('show_page_heading') and $params->get('show_title')) :?>
<hgroup>
<?php endif; ?>
<h1>
	<?php echo $this->escape($this->params->get('page_heading')); ?>
</h1>
<?php endif; ?>
<?php
if (!empty($this->item->pagination) && $this->item->pagination && !$this->item->paginationposition && $this->item->paginationrelative) {
    echo $this->item->pagination;
}

if ($params->get('show_title')) : ?>
		<h2>
			<?php echo $this->escape($this->item->title); ?>
		</h2>
<?php endif; ?>
<?php if ($this->params->get('show_page_heading') and $params->get('show_title')) :?>
</hgroup>
<?php endif; ?>

<?php if ($params->get('access-edit') ||  $params->get('show_print_icon') || $params->get('show_email_icon')) : ?>
		<ul class="actions">
		<?php if (!$this->print) : ?>
				<?php if ($params->get('show_print_icon')) : ?>
				<li class="print-icon">
						<?php echo JHtml::_('icon.print_popup', $this->item, $params, array(), true); ?>
				</li>
				<?php endif; ?>

				<?php if ($params->get('show_email_icon')) : ?>
				<li class="email-icon">
						<?php echo JHtml::_('icon.email', $this->item, $params, array(), true); ?>
				</li>
				<?php endif; ?>
				<?php if ($this->user->authorise('core.edit', 'com_content.article.' . $this->item->id)) : ?>
						<li class="edit-icon">
							<?php echo JHtml::_('icon.edit', $this->item, $params, array(), true); ?>
						</li>
					<?php endif; ?>
		<?php else : ?>
				<li>
						<?php echo JHtml::_('icon.print_screen', $this->item, $params, array(), true); ?>
				</li>
		<?php endif; ?>
		</ul>
<?php endif; ?>

	<?php  if (!$params->get('show_intro')) :
        echo $this->item->event->afterDisplayTitle;
    endif; ?>

	<?php echo $this->item->event->beforeDisplayContent; ?>

<?php $useDefList = ($params->get('show_author') or $params->get('show_category') or $params->get('show_parent_category')
    or $params->get('show_create_date') or $params->get('show_modify_date') or $params->get('show_publish_date')
    or $params->get('show_hits')); ?>

<?php if ($useDefList) : ?>
 <dl class="article-info">
 <dt class="article-info-term"><?php  echo JText::_('COM_CONTENT_ARTICLE_INFO'); ?></dt>
<?php endif; ?>
<?php if ($params->get('show_parent_category') && $this->item->parent_slug !== '1:root') : ?>
		<dd class="parent-category-name">
			<?php 	$title = $this->escape($this->item->parent_title);
                    $url = '<a href="' . JRoute::_(ContentHelperRoute::getCategoryRoute($this->item->parent_slug)) . '">' . $title . '</a>';?>
			<?php if ($params->get('link_parent_category') and $this->item->parent_slug) : ?>
				<?php echo JText::sprintf('COM_CONTENT_PARENT', $url); ?>
				<?php else : ?>
				<?php echo JText::sprintf('COM_CONTENT_PARENT', $title); ?>
			<?php endif; ?>
		</dd>
<?php endif; ?>
<?php if ($params->get('show_category')) : ?>
		<dd class="category-name">
			<?php 	$title = $this->escape($this->item->category_title);
                    $url = '<a href="' . JRoute::_(ContentHelperRoute::getCategoryRoute($this->item->catslug)) . '">' . $title . '</a>';?>
			<?php if ($params->get('link_category') and $this->item->catslug) : ?>
				<?php echo JText::sprintf('COM_CONTENT_CATEGORY', $url); ?>
				<?php else : ?>
				<?php echo JText::sprintf('COM_CONTENT_CATEGORY', $title); ?>
			<?php endif; ?>
		</dd>
<?php endif; ?>
<?php if ($params->get('show_create_date')) : ?>
		<dd class="create">
		<?php echo JText::sprintf('COM_CONTENT_CREATED_DATE_ON', JHtml::_('date', $this->item->created, JText::_('DATE_FORMAT_LC2'))); ?>
		</dd>
<?php endif; ?>
<?php if ($params->get('show_modify_date')) : ?>
		<dd class="modified">
		<?php echo JText::sprintf('COM_CONTENT_LAST_UPDATED', JHtml::_('date', $this->item->modified, JText::_('DATE_FORMAT_LC2'))); ?>
		</dd>
<?php endif; ?>
<?php if ($params->get('show_publish_date')) : ?>
		<dd class="published">
		<?php echo JText::sprintf('COM_CONTENT_PUBLISHED_DATE_ON', JHtml::_('date', $this->item->publish_up, JText::_('DATE_FORMAT_LC2'))); ?>
		</dd>
<?php endif; ?>
<?php if ($params->get('show_author') && !empty($this->item->author)) : ?>
	<dd class="createdby">
		<?php $author = $this->item->author; ?>
		<?php $author = ($this->item->created_by_alias ?: $author);?>
		<?php if (!empty($this->item->contact_link) &&  $params->get('link_author') == true) : ?>
			<?php echo JText::sprintf('COM_CONTENT_WRITTEN_BY', JHtml::_('link', $this->item->contact_link, $author)); ?>
		<?php else : ?>
			<?php echo JText::sprintf('COM_CONTENT_WRITTEN_BY', $author); ?>
		<?php endif; ?>
	</dd>
<?php endif; ?>
<?php if ($params->get('show_hits')) : ?>
		<dd class="hits">
		<?php echo JText::sprintf('COM_CONTENT_ARTICLE_HITS', $this->item->hits); ?>
		</dd>
<?php endif; ?>
<?php if ($useDefList) : ?>
 </dl>
<?php endif; ?>

	<?php if (isset($this->item->toc)) : ?>
		<?php echo $this->item->toc; ?>
	<?php endif; ?>

<?php if (isset($urls) and ((!empty($urls->urls_position) and ($urls->urls_position == '0')) or ($params->get('urls_position') == '0' and empty($urls->urls_position)))
        or (empty($urls->urls_position) and (!$params->get('urls_position')))) : ?>

	<?php echo $this->loadTemplate('links'); ?>
<?php endif; ?>
	<?php  if (isset($images->image_fulltext) and !empty($images->image_fulltext)) : ?>
	<?php $imgfloat = empty($images->float_fulltext) ? $params->get('float_fulltext') : $images->float_fulltext; ?>

	<div class="img-fulltext-<?php echo htmlspecialchars($imgfloat, ENT_COMPAT, 'UTF-8'); ?>">
	<img
		<?php if ($images->image_fulltext_caption):
            echo 'class="caption"'.' title="' .htmlspecialchars($images->image_fulltext_caption, ENT_COMPAT, 'UTF-8') .'"';
        endif; ?>
		src="<?php echo htmlspecialchars($images->image_fulltext, ENT_COMPAT, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($images->image_fulltext_alt, ENT_COMPAT, 'UTF-8'); ?>"/>
	</div>
	<?php endif; ?>
<?php
if (!empty($this->item->pagination) and $this->item->pagination and !$this->item->paginationposition and !$this->item->paginationrelative):
    echo $this->item->pagination;
endif;
?>
<?php if ($params->get('access-view')):?>
	<?php echo $this->item->text; ?>
	<?php // Optional teaser intro text for guests?>
	<?php elseif ($params->get('show_noauth') == true && $user->get('guest')) : ?>
		<?php echo JLayoutHelper::render('joomla.content.intro_image', $this->item); ?>
	<?php echo JHtml::_('content.prepare', $this->item->introtext); ?>
	<?php // Optional link to let them register to see the whole article.?>
	<?php if ($params->get('show_readmore') && $this->item->fulltext != null) : ?>
		<?php $menu = JFactory::getApplication()->getMenu(); ?>
		<?php $active = $menu->getActive(); ?>
		<?php $itemId = $active->id; ?>
		<?php $link = new JUri(JRoute::_('index.php?option=com_users&view=login&Itemid=' . $itemId, false)); ?>
		<?php $link->setVar('return', base64_encode(ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catid, $this->item->language))); ?>
		<p class="readmore">
			<a href="<?php echo $link; ?>" class="register">
			<?php $attribs = json_decode($this->item->attribs); ?>
			<?php if ($attribs->alternative_readmore == null) : ?>
				<?php echo JText::_('COM_CONTENT_REGISTER_TO_READ_MORE'); ?>
			<?php elseif ($readmore = $attribs->alternative_readmore) : ?>
				<?php echo $readmore; ?>
				<?php if ($params->get('show_readmore_title', 0) != 0) : ?>
					<?php echo JHtml::_('string.truncate', ($this->item->title), $params->get('readmore_limit')); ?>
				<?php endif; ?>
			<?php elseif ($params->get('show_readmore_title', 0) == 0) : ?>
				<?php echo JText::sprintf('COM_CONTENT_READ_MORE_TITLE'); ?>
			<?php else : ?>
				<?php echo JText::_('COM_CONTENT_READ_MORE'); ?>
				<?php echo JHtml::_('string.truncate', ($this->item->title), $params->get('readmore_limit')); ?>
			<?php endif; ?>
			</a>
		</p>
	<?php endif; ?>
<?php endif; ?>
<?php // TAGS?>
<?php if ($params->get('show_tags', 1) && !empty($this->item->tags->itemTags)) : ?>
	<?php $this->item->tagLayout = new JLayoutFile('joomla.content.tags'); ?>
	<?php echo $this->item->tagLayout->render($this->item->tags->itemTags); ?>
<?php endif; ?>
<?php if (!empty($this->item->pagination) and $this->item->pagination and $this->item->paginationposition and!$this->item->paginationrelative) : ?>
	<?php echo $this->item->pagination; ?>
<?php endif; ?>
	<?php if (isset($urls) and ((!empty($urls->urls_position) and ($urls->urls_position == '1')) or ($params->get('urls_position') == '1'))) : ?>
		<?php echo $this->loadTemplate('links'); ?>
	<?php endif; ?>
<?php if (!empty($this->item->pagination) and $this->item->pagination and $this->item->paginationposition and $this->item->paginationrelative) : ?>
	<?php echo $this->item->pagination; ?>
<?php endif; ?>
	<?php echo $this->item->event->afterDisplayContent; ?>
</article>
