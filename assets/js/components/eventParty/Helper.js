'use strict';

import $ from 'jquery';

class Helper {

    constructor() {
        this.$epStatus = $('.event-party-status');
        this.$planNavTab = $('#nav-plan-tab');
        this.$participantsWrapper = $('.participants-wrapper');
        this.$meetingOffersHistory = $('.meeting-point-offers-history');

        this.$reserveRequiredAlert = $('.reserve-required-alert');

        this.$planTime  = $('.plan-time');
        this.$planPlace = $('.plan-place');
        this.$planCafe  = $('.plan-near-cafe');

        this.$loadSpinner = $('.js-data').find('.load-spinner');
    }

    addUserIconBlock(userId, avatarPath, nickname) {
        const $userIconBlock = this.$participantsWrapper.find('.user-icon-block').first().clone();

        $userIconBlock.removeClass('current-user').addClass(`js-user-icon-block-${userId}`);
        $userIconBlock.find('.user-icon-img').attr('src', `/${avatarPath}`);
        $userIconBlock.find('.user-icon-name').html(nickname);

        this.$participantsWrapper.append($userIconBlock);
    }

    removeUserIconBlock(userId) {
        $(`.js-user-icon-block-${userId}`).remove();
    }

    updateEpStatus(status) {
        this.$epStatus.html(status);
    }

    openTab(name) {
        $('#nav-plan-tab').addClass('d-none');
        $('#nav-plan').addClass('d-none');

        $('.event-party-tabs-wrapper').find('.nav-item')
            .removeClass('active show')
            .attr('aria-selected', 'false');

        $('.nav-block-content-wrapper').find('.tab-pane')
            .removeClass('active show');

        $(`#nav-${name}-tab`)
            .removeClass('d-none')
            .addClass('active show')
            .attr('aria-selected', 'true');

        $(`#nav-${name}`)
            .removeClass('d-none')
            .addClass('active show');
    }

    hideTab(name) {
        $(`#nav-${name}-tab`).addClass('d-none');
        $(`#nav-${name}`).addClass('d-none');
    }

    showReserveRequiredAlert() {
        if (this.$reserveRequiredAlert.hasClass('d-none')) {
            this.$reserveRequiredAlert.removeClass('d-none');
            this.updatePlanAlertsCount(+1);
        }
    }

    hideReserveRequiredAlert() {
        if (!this.$reserveRequiredAlert.hasClass('d-none')) {
            this.$reserveRequiredAlert.addClass('d-none');
            this.updatePlanAlertsCount(-1);
        }
    }

    addMeetingPointOfferAlert(offerId, lines) {
        let $meetingPointAlert = $('.meeting-point-offer-alert-template .meeting-point-alert').clone();
        $meetingPointAlert.attr('data-offer-id', offerId);

        const $alertText = $meetingPointAlert.find('.meeting-point-text');
        $alertText.html('');

        for (let [key, text] of Object.entries(lines)) {
            let $line = $('<div>').html(text);

            if (key === 'point') {
                $line.addClass('font-weight-bold');
            }

            $alertText.append($line);
        }

        $('.tab-plan-body').prepend($meetingPointAlert);

        this.updatePlanAlertsCount(+1);
    }

    removeMeetingPointOfferAlert(offerId) {
        let $alert = $(`.meeting-point-alert[data-offer-id=${offerId}]`);

        if ($alert.length) {
            $alert.remove();
            this.updatePlanAlertsCount(-1);
        }
    }

    updatePlanAlertsCount(number) {
        let $badge = this.$planNavTab.find('.badge');

        if (!$badge.length) {
            $badge = $('<span>').addClass('badge badge-info').html('0');
            this.$planNavTab.append($badge);
        }

        const newCount = +$badge.html() + number;

        if (newCount <= 0) {
            $badge.remove();
            return;
        }

        $badge.html(newCount);
    }

    addMeetingPointOfferHistoryRow(offerId, meetingPointText, isOfferFromWeb = false) {
        const $meetingPointOffer = $('.meeting-point-offers-history-template .meeting-point-offer').clone();
        $meetingPointOffer.addClass(`meeting-point-offer-${offerId}`);
        $meetingPointOffer.find('.meeting-point-offer-text').html(meetingPointText);

        $('#meeting-point-offers').prepend($meetingPointOffer);

        if (isOfferFromWeb) {
            $meetingPointOffer.find('.meeting-point-offer-accepted-answers-count').html('0');
        }

        this.updateMeetingPointHistoryCount(+1);
    }

    updateMeetingPointHistoryCount(number) {
        let newCount = +this.$meetingOffersHistory.find('.meeting-point-offers-history-count').html() + number;

        this.$meetingOffersHistory.find('.meeting-point-offers-history-count').html(newCount);

        newCount > 0
            ? this.$meetingOffersHistory.removeClass('d-none')
            : this.$meetingOffersHistory.addClass('d-none');
    }

    addAcceptedAnswerToMeetingPointHistoryRow(offerId) {
        const $offer         = $(`.meeting-point-offer-${offerId}`);
        const $acceptedCount = $offer.find('.meeting-point-offer-accepted-answers-count');
        const $totalCount    = $offer.find('.meeting-point-offer-total-users-count');

        $acceptedCount.html(+$acceptedCount.html() + 1);

        if (+$totalCount.html() === +$acceptedCount.html()) {
            $offer.css('color', 'green');
            $offer.find('.meeting-point-offer-icon')
                .removeClass('fa-clock-o')
                .addClass('fa-check');
        }
    }

    markMeetingPointHistoryRowAsRejected(offerId) {
        const $offer = $(`.meeting-point-offer-${offerId}`);

        $offer.css('color', 'red');
        $offer.find('.meeting-point-offer-icon')
            .removeClass('fa-clock-o')
            .addClass('fa-close');
    }

    updateMeetingDateTime(meetingDateTime) {
        this.$planTime.html(meetingDateTime);
    }

    updateMeetingPlace(meetingPlace) {
        this.$planPlace.html(meetingPlace);
    }

    resetPlanTab() {
        const $badge = this.$planNavTab.find('.badge');

        if ($badge) {
            $badge.remove();
        }

        $('#meeting-point-offers').html('');
        $('.meeting-point-offers-history').addClass('d-none');
        $('.meeting-point-offers-history-count').html('0');

        $('.tab-plan-body').find('.meeting-point-alert').remove();

        this.$planTime.html(this.$planTime.data('default'));
        this.$planPlace.html(this.$planPlace.data('default'));

        this.hideOfferBtn();

        this.$planCafe.addClass('d-none');
    }

    hideOfferBtn() {
        $('.meeting-point-offer-btn-wrapper').addClass('d-none');
    }

    showOfferBtn() {
        $('.meeting-point-offer-btn-wrapper').removeClass('d-none');
    }

    showNearCafe() {
        this.$planCafe.removeClass('d-none');
    }
}

export default Helper;
