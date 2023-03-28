<?php

namespace App\Library;

use File;
use Illuminate\Support\Str;
use App\Library\TextHelper;
use Illuminate\Support\Facades\Storage;

class TypeHelper
{
    const INDENT = '    ';

    public static function getTypeName($string)
    {
        $string = ucwords($string);
        $string = str_replace(' ', '', $string);
        $string = str_replace('-', '', $string);
        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string);
        $string = Str::singular($string);
        $typeName = ucfirst($string);

        return $typeName;
    }


    public static function getMutationName($string)
    {
        $string = ucwords($string);
        $string = str_replace(' ', '', $string);
        $string = str_replace('-', '', $string);
        $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string);
        $string = Str::singular($string);
        $typeName = ucfirst($string);

        return $typeName;
    }


    public static function makeType($typeName, $fields, $dataTypes)
    {
        // Make model using command
        \Artisan::call('make:type ' . $typeName . ' --fields='.$fields.' --types='.$dataTypes);

        $filename = base_path('app/GraphQL/Type/' . $typeName . '.php');

        return $filename;
    }

    public static function makeQuery($queryName, $fields, $dataTypes)
    {
        // Make model using command
        \Artisan::call('make:query ' . $queryName . ' --fields='.$fields.' --types='.$dataTypes);
        $filename = base_path('app/GraphQL/Query/' .str_replace('Query','',$queryName));

        return $filename;
    }

    public static function makeMutation($mutationName, $fields)
    {
        // Make model using command
        \Artisan::call('make:mutation ' . $mutationName . ' --fields='.$fields);
        $filename = base_path('app/GraphQL/Mutation/' .str_replace('Mutation','',$mutationName));

        return $filename;<?php if(in_array('vendor', $facilityPersona)): ?>
                                                                <form action="engine/api-settings.php" method="POST" id="vendorQueForm">
                                                                    <div class="panel panel-default card" style="margin-top:5px;padding:25px;">
                                                                        <div class="panel-heading">
                                                                            <h4 class="panel-title">
                                                                                <a data-toggle="collapse" data-parent="#accordion" href="#collapse4">
                                                                                    Vendor Questions</a>
                                                                            </h4>
                                                                        </div>
                                                                        <div id="collapse4" class="panel-collapse collapse card-body" style="margin-top:15px;border-radius: 5px;background-color:#f6f7fb;">
                                                                            <div class="panel-body">
                                                                                <input type="hidden" name="action" value="UPDATE_VENDOR_QUESTIONS">
                                                                                <?php for ($i = 1; $i <= 18; $i++) { ?>
                                                                                    <div class="form-group rephelp row queSec">
                                                                                        <label class="col-1 col-form-label" style="display:none;"></label>
                                                                                        <div class="col-12">
                                                                                            <textarea class="form-control" name="<?= "q{$i}" ?>" placeholder="Add your question here (optional)"><?= $appQsVendorData["q{$i}"] ?></textarea><br>
                                                                                            <input type="text" class="form-control" name="<?= "q{$i}l" ?>" placeholder="Sub-text (optional, appears below the question)" value="<?= $appQsVendorData["q{$i}l"] ?>"><br>

                                                                                            <div class="form-check" style="margin-top:5px;display:inline-block;">
                                                                                                <input class="form-check-input preventCheckbox" id="prevent_<?= "q{$i}f" ?>_YES_VENDOR" name="<?= "q{$i}f" ?>" value="1" type="checkbox" <?= $appQsVendorData["q{$i}f"] == 1 ? 'checked' : '' ?> data-checked="<?= $appQsVendorData["q{$i}f"] == '2' ? '1' : '0' ?>">
                                                                                                <label class="form-check-label" for="prevent_<?= "q{$i}f" ?>_YES_VENDOR">
                                                                                                    <small>Prevent Entry on 'Yes'</small>
                                                                                                </label>
                                                                                            </div>

                                                                                            <div class="form-check" style="margin-top:5px;margin-left:30px;display:inline-block;">
                                                                                                <input class="form-check-input preventCheckbox" id="prevent_<?= "q{$i}f" ?>_NO_VENDOR" name="<?= "q{$i}f" ?>" value="2" type="checkbox" <?= $appQsVendorData["q{$i}f"] == 2 ? 'checked' : '' ?>>
                                                                                                <label class="form-check-label" for="prevent_<?= "q{$i}f" ?>_NO_VENDOR">
                                                                                                    <small>Prevent Entry on 'No'</small>
                                                                                                </label>
                                                                                            </div>

                                                                                            <div class="form-check" style="margin-top:5px;margin-left:150px;display:inline-block;">
                                                                                                <input class="form-check-input subtextCheckbox" id="subtext_<?= "q{$i}s" ?>_OPEN_VENDOR" name="<?= "q{$i}s" ?>" value="1" type="checkbox" <?= $appQsVendorData["q{$i}s"] == 1 ? 'checked' : '' ?>>
                                                                                                <label class="form-check-label" for="subtext_<?= "q{$i}s" ?>_OPEN_VENDOR">
                                                                                                    <small>Open Subtext</small>
                                                                                                </label>
                                                                                            </div>

                                                                                            <small>
                                                                                                <span class="pull-right" style="margin-top:8px;margin-bottom:4px;cursor:pointer;">
                                                                                                    <div class="visibleCheckbox">
                                                                                                        <?php if (!empty($appQsVendorData["q{$i}v"]) && $appQsVendorData["q{$i}v"] == '1') { ?>
                                                                                                            <input type="hidden" name="<?= "q{$i}v" ?>" data-checked="1" value="<?= !empty($appQsVendorData["q{$i}v"]) ? $appQsVendorData["q{$i}v"] : '0' ?>">
                                                                                                            <i class="fa fa-eye" aria-hidden="true" style="color:#DA195C;"></i>&nbsp;
                                                                                                            <span>Visible</span>
                                                                                                        <?php } else { ?>
                                                                                                            <input type="hidden" name="<?= "q{$i}v" ?>" data-checked="0" value="<?= !empty($appQsVendorData["q{$i}v"]) ? $appQsVendorData["q{$i}v"] : '0' ?>">
                                                                                                            <i class="fa fa-eye-slash" aria-hidden="true" style="color:#DA195C;"></i>&nbsp;
                                                                                                            <span>Hidden</span>
                                                                                                        <?php } ?>
                                                                                                    </div>
                                                                                                </span>
                                                                                            </small>
                                                                                        </div>
                                                                                    </div><br>
                                                                                <?php } ?>

                                                                                <div class="">
                                                                                    <button type="submit" class="btn btn-primarybtn waves-effect waves-light btn-lg btn-rounded btn-primary pull-right">Update Clipboard Questions</button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                                <?php endif; ?>
    }

    public static function makeQueryCollection($queryName, $fields, $dataTypes)
    {
        $fields = str_replace(' ,',',',$fields);
        // Make model using command
        \Artisan::call('make:query-collection ' . $queryName . ' --fields='.$fields.' --types='.$dataTypes);
        $filename = base_path('app/GraphQL/Query/' .str_replace('CollectionQuery','',$queryName));

        return $filename;
    }
}
