<?php   defined('C5_EXECUTE') or die("Access Denied."); 
use Application\Controller\Training\Dashboard\Discounts;
use Application\Controller\Training\Member;
use Application\Controller\Training\Shopping;
$h = Loader::helper('concrete/dashboard');
$nh = Loader::helper('navigation');
$currentPage = Page::getCurrentPage();
$db = Loader::db();
echo $h->getDashboardPaneHeaderWrapper(t("Discounts"));
?>

<input id="dialog_owner" type="hidden" name="dialog_owner" value=""/>
<div class="ccm-dashboard-header-buttons">
</div>
<fieldset>
    <legend>Create a Discount</legend>
    <form class="form-horizontal" method="post" action="">
        <div class="row">
            <div class="col-xs-12 col-sm-6">
                <div class="row">
                    <div class="form-group">
                        <label class="col-xs-3">Name:</label>
                        <div class="col-xs-9">
                            <input type="text" class="form-control" name="dName" placeholder="Discount Name">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-3">Code:</label>
                            <div class="col-xs-9">
                                <input type="text" class="form-control" name="dCode" placeholder="Discount Code">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 col-sm-6">
                                    <div class="form-group">
                                        <label class="col-xs-4">Uses:</label>
                                        <div class="col-xs-8">
                                            <input type="number" class="form-control" name="dRemain" value='9999'>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-sm-6">
                                        <div class="form-group">
                                            <label class="col-xs-4">Used:</label>
                                            <div class="col-xs-8">
                                                <input type="number" class="form-control" name="dUsed" value='0'>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-12">
                                        <div class="form-group" style="text-align:center">
                                            <label class="radio-inline">
                                                <input type="radio" name="dType" id="inlineRadio1" value="percent"> Percentage
                                                </label>
                                                <label class="radio-inline">
                                                    <input type="radio" name="dType" id="inlineRadio2" value="amount"> Dollar
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="dType" id="inlineRadio3" value="bundle"> Bundle
                                                        </label>
                                                    </div>
                                                </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <div class="row">
                                                  <div class="col-xs-12">
                                                    <h4>Quantity Thresholds</h3>
                                                    <div class="row quantity-row">
                                                        <div class="col-xs-5">
                                                            <div class="row">
                                                                <div class="col-xs-3">
                                                                    <div class="drop-button">
                                                                        <input type="hidden" name="comparison[]" value="equal" />
                                                                        <button type="button" class="comparator-drop-down btn btn-default  dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">=                                                                        </button>
                                                                        <ul class="dropdown-menu">
                                                                            <li>
                                                                                <a href="#" class="comparison-select" data-value="equal">=</a>
                                                                            </li>
                                                                            <li>
                                                                                <a href="#" class="comparison-select" data-value="greater">></a>
                                                                            </li>
                                                                            <li>
                                                                                <a href="#" class="comparison-select" data-value="less">
                                                                                    <</a>
                                                                                    </li>
                                                                                </ul>
                                                                            </div>
                                                                        </div>
                                                                        
                                                                        <div class="col-xs-9">
                                                                            <input type="number" class="form-control" name="quantityVal[]" value="1">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                            
                                                                    <div class="col-xs-6">
                                                                        <div class="input-group">
                                                                            <div class="input-group-addon quant-dollar">$</div> 
                                                                            <input type="number" class="form-control" name="quantityDisc[]" value="">
                                                                                <div class="input-group-addon quant-percent">% off</div>
                                                                                
                                                                            </div>
                                                                           
                                                                        </div>
                                                                        <div class="col-xs-1">
                                                                         <button class="btn btn-danger deleteRow">X</button>
                                                                        </div>
                                                                    </div>
                                                                    <button class="btn" id="addThresh">Add Another</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                        <div class="col-xs-12">
                                                        <h4>Courses</h4>
                                                        <?php foreach ($courses as $course){ ?>
                                                            <div class="col-xs-12 col-sm-6"><label class="checkbox-inline"><input type="checkbox" name="course[]" value="<?=$course['tablePrefix'];?>_<?=$course['typeID'];?>"><?=$course['name'];?></label></div>
                                                        <?php } ?>
                                                        </div>
                                                        </div>
                                                    </div>
                                                    <input type="hidden" name="action" value="add"/>
                                                    <button class="btn btn-primary" type="submit">Create Discount</button>
                                                </form>
                                            </fieldset>

                                            <?php 
echo $h->getDashboardPaneFooterWrapper(false);?>
                                            