'use strict';
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
/*
 * Nexmo Client SDK
 *  Application Events Handler
 *
 * Copyright (c) Nexmo Inc.
 */
const loglevel_1 = require("loglevel");
const nxmEvent_1 = __importDefault(require("../events/nxmEvent"));
const nxmCall_1 = __importDefault(require("../modules/nxmCall"));
const utils_1 = __importDefault(require("../utils"));
const rtc_helper_1 = __importDefault(require("../modules/rtc_helper"));
/**
 * Handle Application Events
 *
 * @class ApplicationEventsHandler
 * @param {Application} application
 * @param {Conversation} conversation
 * @private
*/
class ApplicationEventsHandler {
    constructor(application) {
        this.log = loglevel_1.getLogger(this.constructor.name);
        this.application = application;
        this._handleApplicationEventMap = {
            'member:joined': this._processMemberJoined,
            'member:invited': this._processMemberInvited
        };
    }
    /**
      * Handle and event.
      *
      * Update the event to map local generated events
      * in case we need a more specific event to pass in the application listener
      * or f/w the event as it comes
      * @param {object} event
      * @private
    */
    handleEvent(event) {
        const conversation = this.application.conversations.get(event.cid);
        const copied_event = Object.assign({}, event);
        if (this._handleApplicationEventMap.hasOwnProperty(event.type)) {
            return this._handleApplicationEventMap[event.type].call(this, conversation, new nxmEvent_1.default(conversation, copied_event), event);
        }
        return new nxmEvent_1.default(conversation, copied_event);
    }
    /**
      * case: call to PSTN, after knocking event we receive joined
      * @private
    */
    _processMemberJoined(conversation, event, raw_event) {
        var _a;
        if (event.body.channel && this.application._call_draft_list.has(event.body.channel.id)) {
            const nxmCall = this.application._call_draft_list.get(event.body.channel.id);
            let pc = ((nxmCall.rtcObjects || {})[event.body.channel.id] || {}).pc;
            nxmCall._setFrom(conversation.me);
            nxmCall._setupConversationObject(conversation, event.body.channel.id);
            // add the media objects to new conversation from placeholder in call
            conversation.media._attachEndingEventHandlers();
            // Checking to see if placeholder NxmCall has rtcObject, pc or activeStreams while new conversation does not and if so add
            // to new conversation the missing rtcObject, pc or activeStream
            if (Object.entries(conversation.media.rtcObjects).length === 0 && Object.entries(nxmCall.rtcObjects).length !== 0) {
                Object.assign(conversation.media.rtcObjects, nxmCall.rtcObjects);
            }
            if (!conversation.media.pc && pc) {
                Object.assign(conversation.media.pc = pc);
            }
            if (conversation.application.activeStreams.length === 0 && nxmCall.application.activeStreams.length > 0) {
                conversation.application.activeStreams = nxmCall.application.activeStreams;
            }
            delete nxmCall.client_ref;
            delete nxmCall.knocking_id;
            // if rtcStats on call object place on media object as well
            if (nxmCall.rtcStats) {
                conversation.media.rtcStats = nxmCall.rtcStats;
            }
            // remove the leg_id from the call_draft_list
            this.application._call_draft_list.delete(event.body.channel.id);
            this.application.calls.set(conversation.id, nxmCall);
            nxmCall._handleStatusChange(event);
            this.application.emit('member:call', this.application.conversations.get(event.cid).members.get((_a = event.body) === null || _a === void 0 ? void 0 : _a.member_id), nxmCall);
            // Supports old way of listening for the media stream after the conversation is set up even though its already there
            setTimeout(() => rtc_helper_1.default.emitMediaStream(conversation.me, pc, nxmCall.stream), 50);
        }
        return event;
    }
    _processMemberInvited(conversation, event) {
        var _a, _b;
        if (!conversation) {
            this.log.warn(`no conversation object for ${event.type}`);
            return event;
        }
        // no need to process the event if it's not media related invite, or the member is us
        if ((conversation.me && (conversation.me.user.id === event.body.invited_by))
            || (!event.body.user.media || !event.body.user.media.audio_settings
                || !event.body.user.media.audio_settings.enabled)) {
            return event;
        }
        const caller = utils_1.default.getMemberNumberFromEventOrNull(event.body.channel) ||
            utils_1.default.getMemberFromNameOrNull(conversation, event.body.invited_by) || 'unknown';
        // (IP - IP call)
        if (conversation.display_name && conversation.display_name.startsWith('CALL_')) {
            const nxmCall = new nxmCall_1.default(this.application, conversation, caller);
            this.application.calls.set(conversation.id, nxmCall);
            this.application.emit('member:call', this.application.conversations.get(event.cid).members.get((_a = event.body) === null || _a === void 0 ? void 0 : _a.member_id), nxmCall);
            // VAPI invites (PHONE - IP)
        }
        else if (!event.body.invited_by) {
            const nxmCall = new nxmCall_1.default(this.application, conversation, caller);
            this.application.calls.set(conversation.id, nxmCall);
            nxmCall._handleStatusChange(event);
            this.application.emit('member:call', this.application.conversations.get(event.cid).members.get((_b = event.body) === null || _b === void 0 ? void 0 : _b.member_id), nxmCall);
        }
        return event;
    }
}
exports.default = ApplicationEventsHandler;
module.exports = ApplicationEventsHandler;
