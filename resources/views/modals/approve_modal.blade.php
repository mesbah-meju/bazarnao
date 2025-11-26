<!-- delete Modal -->
<div id="approve-modal" class="modal fade">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title h6">{{translate('Approve Confirmation')}}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body text-center">
            <img style="width:50%" src="{{ static_asset('assets/img/sad.png') }}">
                <p class="mt-1">{{translate('Are you sure to approve this?')}}</p>
                <button type="button" class="btn btn-link mt-2" data-dismiss="modal">{{translate('Cancel')}}</button>
                <a href="" id="approve-link" class="btn btn-primary mt-2">{{translate('Approve')}}</a>
            </div>
        </div>
    </div>
</div><!-- /.modal -->
