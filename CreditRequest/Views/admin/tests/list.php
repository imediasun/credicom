<h1>Tests</h1>
<br/><a target="_blank" href="/admin/credit-request/tests/test-generate">Sigma Generate</a>
<br/><a target="_blank" href="/admin/credit-request/tests/test-sigma-send-generated">Sigma Send Generated</a>
<br/><a target="_blank" href="/admin/credit-request/tests/test-receive">Sigma Receive</a>
<br/><a target="_blank" href="/admin/credit-request/tests/test-clarification-timeout">Sigma Clarification Timeout</a>
<!--<br><a target="_blank" href="/admin/credit-request/tests/test-email">Send Test Email via SMTP</a>-->
<!--<br><a target="_blank" href="/admin/credit-request/tests/test-epost">Send Epost Email</a>-->
<br/><a target="_blank" href="/admin/credit-request/tests/stepper-loan-notification">Stepper Loan Notification</a>
<br/><a target="_blank" href="/admin/credit-request/tests/test-co-applicant-data-request">CoApplicantDataRequest - Cron</a>




<style>
    #testAuxmoneyForm {
        max-width: 500px;
    }
    #testAuxmoneyForm>fieldset{
        padding: 10px;
    }
    #testAuxmoneyForm input[type="radio"]:disabled + label {
        color: lightgrey;
    }
</style>

<br/>
<form id="testAuxmoneyForm" action="/admin/credit-request/tests/test-auxmoney-push-api" method="POST" target="_blank">
    <fieldset>
        <legend>Send request to Auxmoney for setting fake progresses and trigger Push API</legend>

        <label for="creditRequestId">Credit request id = </label>
        <select size="1" name="creditRequestId" id="creditRequestId" required >
            <option value="">...</option>
            <?php foreach($auxmoneyListForSelect as $creditRequestId => $auxmoneyItem): ?>  
                <option value="<?= $creditRequestId; ?>"
                        data-applicant="<?= (isset($auxmoneyItem[1])) ? $auxmoneyItem[1] : ''; ?>"
                        data-coapplicant="<?= (isset($auxmoneyItem[0])) ? $auxmoneyItem[0] : ''; ?>"
                >
                    <?= $creditRequestId; ?>
                </option>
            

            <?php endforeach; ?>    
        </select>
        <br>

        <input type="radio" name="auxmoneyCreditId" value="0" id="applicant" disabled="" required />
        <label for="applicant">main applicant <span class="additionalInfo"></span></label>
        <br>
        <input type="radio" name="auxmoneyCreditId" value="1" id="coapplicant" disabled="" required />
        <label for="coapplicant">co applicant <span class="additionalInfo"></span></label>
        <br><br>

        <fieldset id="progressNameList">
            <legend>Progress name</legend>
            <?php foreach($auxmoneyProgressList as $progressName): ?> 
                <label>
                    <input type="checkbox" name="progressNameList[]" value="<?= $progressName; ?>" required >
                    <?= $progressName; ?>
                </label>
                <br>
            <?php endforeach; ?>
            <br>
        </fieldset>
        <br>

        <input type="submit" value="Send">
    </fieldset>
</form>

<script>
    $(document).ready(function() {
        
        $(document).on('change', '#testAuxmoneyForm #creditRequestId', function(e) {        
            var optionData = $(this).find(":selected").data();

            $.map(optionData, function(value, index) { 
                if(value > 0) {
                    $('#testAuxmoneyForm input:radio#' + index).removeAttr('disabled').val(value);
                    $('label[for=' + index + '] span.additionalInfo').html(' (auxmoney_reply.credit_id = ' + value + ')');
                } else {
                    $('#testAuxmoneyForm input:radio#' + index).attr('disabled','disabled').prop('checked',false).val('0');
                    $('label[for=' + index + '] span.additionalInfo').html('');
                }
            });
        });
        
        var requiredCheckboxes = $('#progressNameList :checkbox[required]');
        requiredCheckboxes.change(function(){
            if(requiredCheckboxes.is(':checked')) {
                requiredCheckboxes.removeAttr('required');
            } else {
                requiredCheckboxes.attr('required', 'required');
            }
        });

    });    
</script>







