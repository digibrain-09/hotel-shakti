

<div class="modal fade" tabindex="-1" role="dialog" id="delete-modals">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            {!! Form::open(['route'=>[$route,$input]]) !!}
            <div class="modal-header">
                <h5 class="modal-title">@lang('common.delete_title')</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>@lang('common.delete_confirmation_msg')</p>
                    @method('delete')
                </div>
                <div class="modal-footer justify-content-between pt-0">
                    <button type="button" class="btn btn-outline-primary btn-lg" data-dismiss="modal">@lang('common.cancel')</button>
                    <button type="submit" class="btn btn-primary btn-lg" data-indication="off">
                        <span class="indicator-label">@lang('common.delete')</span>
                        <span class="indicator-progress">@lang('common.please_wait')</span>
                        <div class="spinner-border" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </button>
                </div>
            </div>
            {!! Form::close() !!}
    </div>
</div>