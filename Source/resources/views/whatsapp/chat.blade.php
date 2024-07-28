@extends('some.layout')

@section('content')
<input type="hidden" name="contactIdOrUid" :value="contactUid" data-contact-uid="{{ $contact->_uid ?? '' }}">

<script>
    document.addEventListener('DOMContentLoaded', function() {
        new Vue({
            el: '#app', // Make sure this matches your Vue app's mount point
            data: {
                contactUid: ''
            },
            mounted() {
                this.contactUid = this.$el.querySelector('[name="contactIdOrUid"]').dataset.contactUid;
            }
        });
    });
</script>
<fieldset class="col-12 p-2">
    <legend>{{ __tr('Assign Team Member') }}</legend>
    <x-lw.input-field 
        id="lwCurrentlyAssignedUserUid" 
        type="selectize" 
        data-form-group-class="mt--4" 
        name="assigned_users_uid" 
        class="custom-select"
        data-selected="{{ $currentlyAssignedUserUid }}" 
        x-model="currentlyAssignedUserUid"
    >
        <x-slot name="selectOptions">
            <option value="">{{ __tr('Not Assigned') }}</option>
            <option value="no_one">{{ __tr('Not Assigned') }}</option>
            @foreach ($vendorMessagingUsers as $vendorMessagingUser)
                <option value="{{ $vendorMessagingUser->_uid }}">
                    {{ $vendorMessagingUser->first_name . ' ' . $vendorMessagingUser->last_name }}
                    @if($vendorMessagingUser->_uid == getUserUID()) 
                        ({{ __tr('You') }})
                    @endif
                </option>
            @endforeach
        </x-slot>
    </x-lw.input-field>
    <div class="">
        <button type="submit" class="btn btn-dark btn-sm mt--1 float-right">{{ __tr('Save') }}</button>
    </div>
</fieldset>

<x-lw.form>
    <template x-if="contact">
        <!-- Ensure all content inside this template is properly closed and not truncated -->
    </template>
</x-lw.form>

@push('head')
    {!! __yesset('dist/emojionearea/emojionearea.min.css', true) !!}
@endpush

@push('appScripts')
    {!! __yesset('dist/emojionearea/emojionearea.min.js', true) !!}
    <script>
    (function($) {
        'use strict';
        
        window.onUpdateContactDetails = function(responseData, callbackParams) {
            var contactUid = $('#lwWhatsAppChatWindow').data('contact-uid');
            var apiUrl = __Utils.apiURL("{{ route('vendor.chat_message.contact.view', ['contactUid' => 'CONTACT_UID_PLACEHOLDER', 'assigned' => (($assigned ?? null) ? 'to-me' : '')]) }}");
            apiUrl = apiUrl.replace('CONTACT_UID_PLACEHOLDER', contactUid);

            __DataRequest.get(apiUrl, {}, function() {});

            if (callbackParams) {
                appFuncs.modelSuccessCallback(responseData, callbackParams);
            }
        };

        window.updateContactInfo = function(responseData) {
            $('#lwCurrentlyAssignedUserUid')[0].selectize.setValue(responseData.data.currentlyAssignedUserUid);
        };

        window.onNewLabelCreated = function(responseData) {
            $('#lwLabelFieldTitle').val('');
        };

        window.updateManageLabelsList = function(responseData) {
            if (responseData.reaction == 1) {
                window.onUpdateContactDetails();
            }
        };

        window.onUpdateContactDetails();

        window.lwMessengerEmojiArea = $(".lw-input-emoji").emojioneArea({
            useInternalCDN: true,
            pickerPosition: "top",
            searchPlaceholder: "{{ __tr('Search') }}",
            buttonTitle: "{{ __tr('Use the TAB key to insert emoji faster') }}",
            events: {
                'emojibtn.click': function (editor, event) {
                    this.hidePicker();
                },
                keyUp: function (editor, event) {
                    if (event && event.which == 13 && !event.shiftKey && $.trim(this.getText())) {
                        $('.lw-input-emoji').val(this.getText());
                        $('#whatsAppMessengerForm').submit();
                        this.hidePicker();
                        appFuncs.resetForm();
                    }
                }
            }
        });
    })(jQuery);
    </script>
@endpush

@endsection