<h2><?php echo tFile::getT('module_install', 'Necessary parameters for the CMS Open Real Estate installation');?></h2>
<table class="result">
    <tr>
        <th><?php echo tFile::getT('module_install', 'Value'); ?></th>
        <th><?php echo tFile::getT('module_install', 'Result'); ?></th>
        <th><?php echo tFile::getT('module_install', 'Comment'); ?></th>
    </tr>
    <?php foreach ($req['requirements'] as $requirement): ?>
    <tr>
        <td width="200"><?php echo $requirement[0]; ?></td>
        <td class="<?php echo $requirement[2] ? 'passed' : ($requirement[1]
            ? 'failed' : 'warning'); ?>">
            <?php echo $requirement[2] ? tFile::getT('module_install', 'OK') : ($requirement[1] ? tFile::getT('module_install', 'Error')
            : tFile::getT('module_install', 'Warning')); ?>
        </td>
        <td><?php echo $requirement[4]; ?></td>
    </tr>
    <?php endforeach;?>
</table>