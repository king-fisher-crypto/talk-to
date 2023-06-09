# Changelog

## 8.1.1

### New

- Add new optional `mediaParams` parameter in `reconnectCall` function, in order to modify the `MediaStream` object

```javascript
application.reconnectCall(
  "conversation_id",
  "rtc_id",
  { audioConstraints: { deviceId: "device_id" } }
).then((nxmCall) => {
  console.log(nxmCall);
}).catch((error) => {
  console.error(error);
});
```

### Fixes

- Assign the correct `NXMCall` status when reconnecting to a call (`STARTED`, `RINGING` or `ANSWERED`)

### Changes

- Update `npm` dependencies

---

## 8.1.0

### New

- Add `reconnectCall` function allowing users to reconnect to a call within 20 seconds if browser tab closed

```javascript
application.reconnectCall("conversation_id", "rtc_id").then((nxmCall) => {
  console.log(nxmCall);
}).catch((error) => {
  console.error(error);
});
```

- Add optional parameter `reconnectRtcId` to media `enable()` function to reconnect media to call

```javascript
conversation.media.enable({ reconnectRtcId: "UUID" }).then((stream) => {
  console.log(stream)
}).catch((error) => {
  console.error("error renabling media", error);
});
```

- Add `custom_data` object in `callServer` function

```javascript
application.callServer("<phone_number>", "phone", { field1: "test" }).then((nxmCall) => {
  console.log(nxmCall);
}).catch((error) => {
  console.error(error);
});
```

- Add `apiKey`, `applicationId`, `conversationId` and `conversationName` when available in `rtcstats` analytics reports

### Fixes

- Fix bug in call transfer where `transferred_from` was undefined

---

## 8.0.5

### Fixes

- Fix the `from` for member events
- Update npm dependencies including `socket-io`

---

## 8.0.4

### Fixes

- Fix bug on `DTMF` dispatch of callback to `event_url`

---

## 8.0.3

### Fixes

- Fix out of order internal events processing in the events queue
- Fix deprecation warning message for `rtcstats:report`

---

## 8.0.1

### Fixes

- Update Typescript definitions
- Fix bug on IP-IP call scenario involving Native SDKs (not populating properly the `Conversation.members` Map)

---

## 8.0.0

### Breaking changes

- Deprecate `Conversation.members` Map (it will be populated only on a call scenario)
- Conversation events will be emitted with a subset information of the Member

```javascript
conversation.on("any:event", ({memberId, userId, userName, displayName, imageUrl, customData}, event) => {});
```

### Changes

- Add paginated `getMembers()` function to retrieve the members of a conversation

```javascript
conversation.getMembers().then((members_page) => {
  members_page.items.forEach(member => {
    render(member);
  })
}).catch((error) => {
  console.error("error getting the members ", error);
});
```

- Add `getMyMember()` function to retrieve our own member in a conversation

```javascript
conversation.getMyMember().then((member) => {
  render(member);
}).catch((error) => {
  console.error("error getting my member", error);
});
```

- Add `getMember()` function to fetch a conversation member by `member_id`

```javascript
conversation.getMember("MEM-id").then((member) => {
  render(member);
}).catch((error) => {
  console.error("error getting member", error);
});
```

### Fixes

- Update Typescript definitions

---

## 7.1.0

### Changes

- `rtcstats:report` is deprecated. Please use `rtcstats:analytics`instead

```ts
application.on('rtcstats:analytics', ({
  type: 'mos' || 'mos_report',
  mos: string,
  rtc_id: string,
  mos_report?: {
    average: string,
    last: string,
    max: string,
    min: string
  },
  report?: RTCStatsReport
}) => {}
```

- Add logs reporter for remote logging
- Add custom `getStats` parser
- Remove `callstats` library and implementation

---

## 7.0.2

### Fixes

- Emit 'call:status:update' event when application offline and call disconnected

---

## 7.0.1

### Fixes

- Remove unnecessary warning message for call status transitions

---

## 7.0.0

### Breaking changes

- `legs` endpoint should be included in `acl` paths on `JWT` token creation

```json
"acl": {
  "paths": {
    ...,
    "/*/legs/**": {}
  }
}
```

### Changes

- Improve `callServer` setup time by prewarming leg
- Add the `rtcObject` and remote `stream` to the `NxmCall` object

### Fixes

- Return `ClientDisconnected` reason when client logouts from SDK

---

## 6.2.1

### Fixes

- Revert back to 6.1.1 from 6.2.0-alpha

---

## 6.1.2

### Fixes

- Update Typescript definitions

---

## 6.1.1

### Fixes

- Update Typescript definitions

---

## 6.1.0

### Changes

- Add `enableEventsQueue` flag to client configuration (default to true)
- Add internal `eventsQueue` mechanism to guarantee order of events received during a session

---

## 6.0.19

### Changes

- Update `reconnectionDelay` to `2000` in `socket.io`
- Add `randomizationFactor` to `0.55` in `socket.io`

---

## 6.0.18

### Changes

- Update documentation

### Fixes

- Add `reconnectionDelay` to 3000 and `reconnectionDelayMax` to 15000 in `socket.io`

---

## 6.0.17

### Fixes

- Update Session id after reconnection

---

## 6.0.16

### Fixes

- Dispatch `system:error:expired-token` event in application level

```javascript
  application.on('system:error:expired-token', 'NXM-errors', () => {
    console.log('Token Expired');
  });
```

- Handle uncaught exception of `conversation:error:invalid-member-state` event

---

## 6.0.15

### Fixes

- Set SDK default to single preset ICE candidate
- Remove `iceGatherOnlyOneCandidate` flag from client configuration

---

## 6.0.13

### Fixes

- Update websocket reconnection logic for token expiry

---

## 6.0.12

### Fixes

- Update resolved stream on media enabling

---

## 6.0.11

### Changes

- Update websocket reconnection logic
- Fix delay in establishing media connection with one ice candidate sent

---

## 6.0.10

### Fixes

- Filter IPv6 Candidates from `iceGatherOnlyOneCandidate` SDP offer

---

## 6.0.9

### Fixes

- Fix handler of `rtc:transfer` event to refresh conversation

---

## 6.0.8

### Fixes

- Fix duplicated webrtc offer sent during IP calling
- Fix Safari `WebRTC` dependency

---

## 6.0.7

### Fixes

- Fix ANSWERED call status in IP - IP calling
- Fix docs issues

### Changes

- Improve TypeScript definitions

---

## 6.0.6

### Fixes

- Add `iceGatherOnlyOneCandidate` configuration option and use to define path in `ICE gathering` process

---

## 6.0.5

### Fixes

- Update styling of `JSDocs` to `Docstrap` template
- Change `RTCPeerConnection ICE candidates` gathering process to send the Session Description Protocol (`SDP`) offer on first `ICE` candidate gathered

---

## 6.0.4

### Fixes

- Remove remaining audio elements after transferring a call to a new conversation
- Update `conversation.invite()` to not include empty `user_id` or `user_name` fields within the requests

---

## 6.0.3

### New

- Added TypeScript definition files

### Changes

- Added options for customized logging levels in the console of `debug`, `info`, `warn`, or `error`.

```javascript
new NexmoClient({
  debug: 'info'
})
```

- Moved storage of JWT token from `localStorage` to `NexmoClient` configuration object
- Removed unnecessary files from the NPM release package

### Fixes

- Fixed call statuses order in case of a transfer

---

## 6.0.1

### Changes

- Removed `media.record()` and `media.fetchRecording()` functions
- Removed cache option from SDK, used for storing conversations and events
- Removed automatic syncing of all individual `conversations` in login, when `sync` is `lite` or `full`

---

## 6.0.0

### Breaking Changes

- Change return value of `application.getConversations()` to new `ConversationsPage` object

```javascript
// iterate through conversations
application
  .getConversations({ page_size: 20 })
  .then((conversations_page) => {
    conversations_page.items.forEach(conversation => {
      render(conversation);
    })
  });
```

- Change return value of `conversation.getEvents()` to new `EventsPage` object

```javascript
// iterate through events
conversation
  .getEvents({ event_type: `member:*` })
  .then((events_page) => {
    events_page.items.forEach(event => {
      render(event);
    })
  });
```

- Rename method `application.callPhone` to `application.callServer`
- Rename method `application.call` to `application.inAppCall`
- Rename method `call.createPhoneCall` to `call.createServerCall`
- Rename class `Call` to `NXMCall`
- Rename class `ConversationClient` to `NexmoClient`
- Rename class `ConversationClientError` to `NexmoClientError`
- Rename files `conversationClient.js` and `conversationClient.min.js` to `nexmoClient.js` and `nexmoClient.min.js`
- Deprecate `member:call:state` event (use instead `member:call:status`)
- Remove automatic login in case of a websocket reconnection and emit the event

### New

- Send and listen for custom event types in a conversation.

```javascript
//sending a custom event type to a conversation
conversation
  .sendCustomEvent({type: `my_custom_event`, body: { enabled: true }})
  .then((custom_event) => {
    console.log(event.body);
  });
```

```javascript
//listening for a custom event type
conversation.on(`my_custom_event`, (from, event) => {
  console.log(event.body);
});
```

- Add new `PageConfig` class for configuring settings for paginated requests
- Add new `Page` class to wrap results of paginated requests
- Add setup of default pagination configuration for conversations and events in ConversationClient initialization
- Add wild card supported for filtering by event types using `:*` (for example `event_type`: `member:*`)

```javascript
new NexmoClient({
  conversations_page_config: {
    page_size: 25,
    order: 'asc'
    cursor: 'abc'
  },
  events_page_config: {
    page_size: 50,
    event_type: `member:*`
  }
})
```

- Add new `ConversationsPage` and `EventsPage` which extend `Page` class to wrap results of paginated requests for conversations and events
- Add `getNext()` and `getPrev()` methods to `ConversationsPage` and `EventsPage` objects to fetch previous and next pages of conversations and events
- Add `conversations_page_last` parameter to `application` object and `events_page_last` parameter to `conversation` object for reference to last page retrieved

```javascript
application.conversations_page_last
  .getNext((conversations_page) => {
    conversations_page.items.forEach(conversation => {
      render(conversation)
    })
  })
```

```javascript
conversation.events_page_last
  .getPrev((events_page) => {
    events_page.items.forEach(event => {
      render(event)
    })
  })
```

- Add the ability to make an IP-IP call through `callServer` function

```javascript
// IP-IP call scenario
application
  .callServer('username', 'app')
  .then((nxmCall) => {
    // console.log(nxmCall);
  });

// IP-PSTN call scenario
application
  .callServer('07400000000')
  .then((nxmCall) => {
    // console.log(nxmCall);
  });
```

### Changes

- Update `reason` object to receive `reason.reason_text` and `reason.reason_code` fields

### Internal changes

- Rename `Event` class to `NXMEvent`
- Update CAPI requests to REST calls for these events
  - `event:delivered`
  - `text:delivered`
  - `image:delivered`
  - `event:seen`
  - `text:seen`
  - `image:seen`
  - `conversation:events`
  - `audio:play`
  - `conversation:delete`
  - `conversation:invite`
  - `text`
  - `text:typing:on`
  - `text:typing:off`
  - `new:conversation`
  - `conversation:get`
  - `user:conversations`
  - `user:get`
  - `conversation:join`
  - `audio:say`
  - `audio:earmuff:on`
  - `audio:earmuff:off`
  - `audio:dtmf`
  - `audio:record`
  - `audio:play`
  - `conversation:member:delete`
  - `event:delete`
  - `audio:ringing:start`
  - `audio:ringing:stop`
  - `audio:mute:on`
  - `audio:mute:off`
  - `image`
  - `rtc:new`
  - `rtc:answer`
  - `rtc:terminate`
  - `knocking:new`
  - `knocking:delete`

---

## 5.3.4

### Fixes

- Custom SDK config object does a deep merge with default config object

---

## 5.3.3

### Fixes

- Change digits to digit in the sendDTMF() request method payload
- Stream is not being terminated on a call transfer
- `member:call` is not being emitted if `media.audio_settings.enabled` is false or doesn't exist

### New

- Set member.callStatus `started` when initialising an IP - IP call
- Set member.callStatus `ringing` when enabling the ringing with `media.startRinging()`

### Internal changes

- Move stream clean up from `member:left` to `rtc:hangup` in Media module

---

## 5.3.2

### Breaking changes

- Revert 5.3.1 pagination back to 5.2.1

---

## 5.3.1

### Patch

- Update expected payload for pagination

---

## 5.3.0

### New

- Migrated from websocket to network requests for conversation.getEvents() & application.getConversations() in preparation for new paginated endpoints

---

## 5.2.1

### New

- Support `reason` for member:delete `conversation.leave`, `member.kick`, `call.hangup` and `call.reject`
- Listen for the `member:left` event with `reason`

```javascript
//listening for member:left with reason
conversation.on('member:left', (member, event) => {
  console.log(event.body.reason);
});
```

```javascript
/**
* Reason object format
*
* @param {object} [reason] the reason for kicking out a member
* @param {string} [reason.code] the code of the reason
* @param {string} [reason.text] the description of the reason
*/
```

- Add `callStatus` field in the `Member` object, defining the status of a call
- Emit `member:call:status` event each time the `member.callStatus` changes

```javascript
conversation.on("member:call:status", (member) => {
   console.log(member.callStatus);
});
```

---

## 5.2.0

### New

- Add the `call` instance in `application.calls` map in `createCall()` function (IP -IP call)

- Update caller parameter in call object in a PSTN - IP call from `unknown` to `channel.from.number` or `channel.from.uri` if exists

- Emit the new `leg:status:update` event each time a member leg status change

```javascript
/**
  * Conversation listening for leg:status:update events.
  *
  * @event Conversation#leg:status:update
  *
  * @property {Member} member - the member whose leg status changed
  * @property {Event} event - leg:status:update event
  * @param {string} event.cid - the conversation id
  * @param {string} event.body.leg_id - the conversation leg id
  * @param {string} event.body.type - the conversation leg type (phone or app)
  * @param {string} event.body.status - the conversation member leg status
  * @param {Array} event.body.statusHistory - array of previous leg statuses
*/
conversation.on("leg:status:update", (member, event) {
  console.log(member, event);
});
```

- Add the the `channel.legs` field in member events offered by CS

```text
conversation.on(<member_event>, (member, event) {
  console.log(event);
  // member_id: <member_id>,
  // conversation_id: <conversation_id>,
  // ...
  // channel: {
  //  to: {
  //    type: app
  //  },
  //  type: app,
  //  leg_ids: [<leg_id>]
  //  legs : [{ leg_id: <leg_id>, status: <leg_status>}],
  //  leg_settings: {},
  // },
  // state: <state>,
  // leg_ids: []
});
```

---

## 5.1.0

### New

- Send DTMF event to a conversation

 ```text
  * Send DTMF in a conversation
  *
  * @param {string} digits - the DTMF digit(s) to send
  * @returns {Promise<Event>}
 ```

```javascript
 conversation.media.sendDTMF('digits')
```

- Emit new event `audio:dtmf`

```javascript
conversation.on("audio:dtmf",(from, event)=>{
  event.digit // the dtmf digit(s) received
  event.from //id of the user who sent the dtmf
  event.timestamp //timestamp of the event
  event.cid // conversation id the event was sent to
  event.body // additional context about the dtmf
});
```

- Set customized audio constraints for IP calls when enabling audio

```javascript
 conversation.media.enable({
    'audioConstraints': audioConstraints
 })
```

```text
  * Replaces the stream's audio tracks currently being used as the sender's sources with a new one with new audio constraints
  * @param {object} constraints - audio constraints
  * @returns {Promise<MediaStream>} - Returns the new stream with the updated audio constraints.
  * @example
  * conversation.media.updateAudioConstraints({'autoGainControl': true})
  **/
```

- Update audio constraints for existing audio tracks

```javascript
  conversation.media.updateAudioConstraints(audioConstraints)
 })
```

### Fixes

- Remove 'this' passed to cache worker event handler

### Internal breaking changes

- Change the media audio parameter from `media.audio` to `media.audio_settings` in `inviteWithAudio` function

---

## 5.0.3

### Changes

- Change default behaviour of `autoPlayAudio` in `media.enable()` from false to true
- Pass an `autoPlayAudio` parameter to `call.createCall()` and `call.answer()` functions (default is true)

---

## 5.0.2

### New

- Delete the image files before sending the `image:delete` request
- Attach of audio stream can now be chosen if it will be automatically on or off through `media.enable()`

```javascript
media.enable({
  autoPlayAudio: true | false
})
```

### Changes (internally)

- Combine the network GET, POST and DELETE requests in one generic function

---

## 5.0.1

### Fixes

- Clean up user's media before leaving from an ongoing conversation

### Breaking changes

- Change `application.conversations` type from `Object` to `Map`

---

## 4.1.0

### Fixes

- Fixed the bug where the audio stream resolved in media.enable() is causing echo and was not the remote stream
- Resolve the remote stream `pc.ontrack()` and not the `localStream` from getUserMedia

### Changes

- Rename `localStream` to `stream` in `media.rtcObjects` object.

---

## 4.0.2

### Changes

- Removed `media.rtcNewPromises`

### New

- Internal lib dependencies update
- Added suport for Bugsnag error monitoring and reporting tool

```text
 * @class ConversationClient
 *
 * @param {object} param.log_reporter configure log reports for bugsnag tool
 * @param {Boolean} param.log_reporter.enabled=false
 * @param {string} param.log_reporter.bugsnag_key your bugsnag api key / defaults to Nexmo api key
 ```

- Updated vscode settings to add empty line (if none) at end of every file upon save
- Disable the ice candidates trickling in ice connection
- Wait until most of the candidates to be gathered both for the local and remote side
- Added new private function `editSDPOrder(offer, answer)` in `rtc_helper.js` to reorder the answer SDP when it's needed
- For rtc connection fail state
  - Disable leg
  - emit new event `media:connection:fail`

```javascript
member.on("media:connection:fail",(connection_details)=>{
  connection_details.rtc_id // my member's call id / leg id
  connection_details.remote_member_id // the id of the Member the stream belongs to
  connection_details.connection_event: // the connection fail event
  connection_details.type // the type of the connection (video or screenshare)
  connection_details.streamIndex // the streamIndex of the specific stream
});
```

```text
* @event Member#media:connection:fail
*
* @property {number} payload.rtc_id the rtc_id / leg_id
* @property {string} payload.remote_member_id the id of the Member the stream belongs to
* @property {event} payload.connection_event the connection fail event
 ```

- Add new LICENCE file

### Breaking changes (internally)

- Deprecating ice trickling logic with `onicecandidate` event handler
- Change the format of `member:media` event to the new one offered by CS

```text
type: 'member:media',
  from: member.member_id,
  conversation_id: member.conversation_id,
  body: {
    media: member.media,
    channel: member.channel
  }
```

- Change the format of `member:invited` event to the new offered by CS

```text
type: 'member:invited',
  body: {
    media: {
      audio_settings: {
        enabled: false,
        earmuffed: false,
        muted: false
      }
    }
  }
```

---

## 4.0.1

### New

- Select the sync level for the login process
  - `full`: trigger full sync to include conversations and events
  - `lite`: trigger partial sync, only conversation objects (empty of events)
  - `none`: don't sync anything

  if the Cache module is enabled the manual fetch of a conversation will store them in internal storage

  usage:

  ```javascript
  new ConverationClient({'sync':'full'});
  ```

### Fixes

- `rtcstats:report` was duplicating instances in each call
- remove `screenshare` https restriction

### Breaking changes (internally)

- Deprecating `application.activeStream`, now it's part of `application.activeStreams`
- Removed the restriction to allow calling `media.enable()` while a stream is active

---

## 4.0.0

### Breaking Changes

- rename SDK `stitch` to `client`
- listening for `media:stream:*` now gives `streamIndex` instead of `index` for consistency with the internal rtcObjects

```text
 * @event Member#media:stream:on
 *
 * @property {number} payload.streamIndex the index number of this stream
 * @property {number} [payload.rtc_id] the rtc_id / leg_id
 * @property {string} [payload.remote_member_id] the id of the Member the stream belongs to
 * @property {string} [payload.name] the stream's display name
 * @property {MediaStream} payload.stream the stream that is activated
 * @property {boolean} [payload.video_mute] if the video is hidden
 * @property {boolean} [payload.audio_mute] if the audio is muted
 ```

### Internal Breaking Changes

### New

- Screen Share Source ID can now be specified when invoking `media.enable()`

---

## 3.0.2

### New

- Get local stream details under member object on `media:stream:on` for audio

```javascript

call.conversation.me.on("media:stream:on",(stream_details)=>{
  // stream_details.rtc_id // my member's call id / leg id
  // stream_details.pc // the PeerConnection object of the instance
  // stream_details.stream: // the local stream object,
  // stream_details.type: 'audio', // the stream type
  // stream_details.index: streamIndex // the stream index
});

```

```text
 * @event Member#media:stream:on
 *
 * @property {number} payload.index the index number of this stream
 * @property {number} [payload.rtc_id] the rtc_id / leg_id
 * @property {string} [payload.remote_member_id] the id of the Member the stream belongs to
 * @property {string} [payload.name] the stream's display name
 * @property {MediaStream} payload.stream the stream that is activated
 * @property {boolean} [payload.video_mute] if the video is hidden
 * @property {boolean} [payload.audio_mute] if the audio is muted
 ```

### Fix

- Resubscribe to events after disabling and re-enabling media (video flow)

### Changes

- update default socket.io `reconnectionAttempts` from `infinity` to `5`

### Breaking changes

- group socket.io configuration params

  e.g. to disable auto-reconnect:  new ConversationClient({socket_io:{
      reconnection:false
  }})

- rename `Stitch-client` errors to `NXM-errors` errors

Example: Listen to general errors

```javascript
            application.on('*', 'NXM-errors', (error) => {
              console.log('An error has been thrown with the type' + error.type);
            });
```

Example: Listen to expired token and update it

```javascript
            application.on('system:error:expired-token', 'NXM-errors', () => {
              application.updateToken(<token>)
                .then(() => {
                  console.log('Token Expired');
                });
            });
```

---

## 3.0.1

### New

- handle call transfer events `rtc:transfer`
  - attach `member.transferred_from = old_conversation` to know that the member has been transferred from another conversation
  - attach `member.transferred_to = new_conversation` to know that this new member comes from a transfer
  - attach `call.transferred` to know that a transfer has happened in this call object

- Added `Call.id` property, when the call is initialised. (leg_id/call_id) (Use this id to transfer this rtc member)

### Fixes

- Now the SDK won't kick the last member of a conversation in a call (it was causing most of the `conversation:not-found` issues when a call was terminated)

### Internal Changes

- moved `conversation.rtcObjects` to the `media` level

it was `conversation.media.parentConversation.rtcObjects`, Now you can access the rtcObjects in the media level `conversation.media.rtcObjects`

- the single PeerConnection object (audio only flow) has also been moved to the media level. `conversation.media.pc`

---

## 3.0.0

### BREAKING CHANGES

- `Conversation.members` is now a `Map()`
- `Call.to` members is now a `Map()`

to get a member object

```javascript

conversation.members.get(member_id);

```

to loop through members

```javascript

for (const member of conversation.members.values()) { ... }

```

---

## 2.2.0

### New

- NPM lib dependencies update

- Added `mos_report` as the last param in ConversationClient#rtcstats:report when the audio stream is disabled.

- support callstats.io

  - set the initial config: `callstats: { enabled: true, AppID: 'xxx', AppSecret: 'yyy' },`

### Fix

- on call hangup the rtc:terminate was sent two times

- fix `media.mute()` for audio stream when the audio is enabled for a second time

### Breaking Changes

- drop support for `development` for Node v5

---

## 2.1.0

### Breaking Changes

- Screen share

  - `screenshareExtensionId` changed to `screenShareExtensionId`

```Javascript
new ConversationClient({
      debug: false,
      screenShareExtensionId: YOUR_EXTENSION_ID
    })
```

- RTC stats

  - config params for rtcstats are now under a single object

before:

```javascript
params.rtcstats_enabled = false
params.rtcstats_url = 'url'
params.rtcstats_events = true
```

now:

```javascript
params.rtcstats.ws_url = 'url'
params.rtcstats.emit_events = true
 ```

### New

- Call stats event

  - initialise by setting initial config: `new ConversationClient({rtcstats.emit_events:true});`

  - listen for MOS for the Audio stream on `Application.on('rtcstats:report', data);`

- Updated webRTC adapter (better support for browsers)

- Internal lib dependency updates

- Call object now has a direction (Outbound, Inbound)

- Add minified version in public bundle `dist/conversationClient.min.js`

### Fixes

- change build/publish flow to include the `dist/conversationClient.min.js`

- covering more use cases: listen your user's join and invite events on both application and conversation level

- Timeout with success for Ice gathering for audio calls (mid solution until trickle is done)

- Fix call status ANSWERED IP-PSTN case, should reflect the PSTN member joining the audio stream

- Call status now does not go to a previous status

- Switch to websocket protocol to fix disconnection issues

### Internal optimisations

- documentation for WebRTC stats reporting to WS server

  - initialise by setting initial config: `new ConversationClient({rtcstats.ws_url:"wss://..."});`

  - the WebSocket should then start receiving webrtc reports every 1s (reconnect timeout: 5000ms, retries 5) not yet configurable

### Known Issues

- media.mute() throws error when the audio has been disabled and enabled again
