import{C as e,D as t,E as n,S as r,T as i,_ as a,b as o,d as s,f as c,g as l,h as u,m as d,p as f,u as p,v as m,w as h,x as g,y as _}from"./pk-copy-button-Dfji6-PV-CnyJ1WOh.js";var v=t`
    @layer pk-component {
        .form-control {
            display: flex;
            flex-direction: column;
            gap: 0.375rem;
            width: 100%;
        }

        .form-control__header {
            display: flex;
            flex-direction: column;
            gap: 0.125rem;
            min-width: 0;
        }

        .form-control__label {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            margin: 0;
            color: var(--pk-color-gray-700);
            font-family: var(--pk-font-family);
            font-size: var(--pk-font-size-base);
            font-weight: 700;
            line-height: var(--pk-line-height);
        }

        .form-control__instructions,
        .form-control__hint {
            margin: 0;
            color: var(--pk-color-gray-500);
            font-family: var(--pk-font-family);
            font-size: var(--pk-font-size-base);
            line-height: var(--pk-line-height);
        }

        .form-control__instructions:empty,
        .form-control__hint:empty {
            display: none;
        }

        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        .form-control__input {
            display: flex;
            align-items: stretch;
            position: relative;
            width: 100%;
        }

        .form-control__start,
        .form-control__end {
            display: inline-flex;
            align-items: center;
            flex-shrink: 0;
        }

        .form-control__start {
            margin-inline-end: 6px;
        }

        .form-control__end {
            margin-inline-start: 6px;
        }

        .icon-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 0;
            border: 0;
            background: transparent;
            color: var(--pk-color-gray-500);
            cursor: pointer;
            line-height: 0;
        }

        .icon-button:focus-visible {
            outline: none;
            box-shadow: var(--pk-shadow-focus);
            border-radius: var(--pk-radius-sm);
        }

        .icon-button:disabled {
            cursor: not-allowed;
            opacity: 0.5;
        }
    }
`,y=[`aria-labelledby`,`aria-describedby`,`aria-invalid`,`aria-errormessage`,`aria-required`,`aria-label`];function b(e){return e.hasAttribute(`aria-labelledby`)||e.hasAttribute(`aria-describedby`)||e.hasAttribute(`aria-errormessage`)}function x(e,t){for(let n of y){let r=e.getAttribute(n);r===null?t.removeAttribute(n):t.setAttribute(n,r)}}function S({control:e,labelId:t,instructionsId:n,hasLabel:r,hasInstructions:i,required:a=!1,invalid:o=!1}){r&&t?e.setAttribute(`aria-labelledby`,t):e.removeAttribute(`aria-labelledby`),i&&n?e.setAttribute(`aria-describedby`,n):e.removeAttribute(`aria-describedby`),a?e.setAttribute(`aria-required`,`true`):e.removeAttribute(`aria-required`),o?e.setAttribute(`aria-invalid`,`true`):e.removeAttribute(`aria-invalid`),e.removeAttribute(`aria-errormessage`)}var C=class{constructor(e,t,n){this.host=e,this.getTarget=t,this.onSync=n}connect(){this.sync(),this.observer=new MutationObserver(()=>{this.sync()}),this.observer.observe(this.host,{attributes:!0,attributeFilter:[...y]})}disconnect(){this.observer?.disconnect(),this.observer=void 0}sync(){if(!b(this.host)){this.onSync?.();return}let e=this.getTarget();e&&x(this.host,e)}},w=class extends Event{constructor(){super(`pk-invalid`,{bubbles:!0,cancelable:!1,composed:!0})}};function T(){return{observedAttributes:[`custom-error`],checkValidity(e){let t={message:``,isValid:!0,invalidKeys:[]};return e.customError&&(t.message=e.customError,t.isValid=!1,t.invalidKeys.push(`customError`)),t}}}var E=class extends o{static{this.formAssociated=!0}static get validators(){return[T()]}static get observedAttributes(){let e=new Set(super.observedAttributes??[]);for(let t of this.validators)for(let n of t.observedAttributes??[])e.add(n);return[...e]}constructor(){super(),this.internals=this.attachInternals(),this.assumeInteractionOn=[`input`],this.validators=[],this.name=null,this.disabled=!1,this.required=!1,this.customError=null,this.valueHasChanged=!1,this.hasInteracted=!1,this.emittedEvents=[],this.emitInvalid=e=>{e.target===this&&(this.hasInteracted=!0,this.dispatchEvent(new w))},this.handleInteraction=e=>{this.emittedEvents.includes(e.type)||this.emittedEvents.push(e.type),this.emittedEvents.length>=this.assumeInteractionOn.length&&(this.hasInteracted=!0,this.updateValidity())},this.addEventListener(`invalid`,this.emitInvalid)}connectedCallback(){super.connectedCallback();for(let e of this.assumeInteractionOn)this.addEventListener(e,this.handleInteraction);this.updateValidity()}disconnectedCallback(){this.hostAriaMirror?.disconnect(),this.hostAriaMirror=void 0;for(let e of this.assumeInteractionOn)this.removeEventListener(e,this.handleInteraction);this.removeEventListener(`invalid`,this.emitInvalid),super.disconnectedCallback()}updated(e){e.has(`customError`)&&this.setCustomValidity(this.customError??``),e.has(`disabled`)&&this.setState(`disabled`,!!this.disabled),(e.has(`value`)||e.has(`checked`)||e.has(`disabled`)||e.has(`required`)||e.has(`name`))&&this.syncFormValue(),this.updateValidity(),super.updated(e),this.syncHostAriaMirror()}firstUpdated(e){super.firstUpdated(e),this.connectHostAriaMirror()}getAriaMirrorTarget(){return this.input??null}syncStandaloneAria(){}connectHostAriaMirror(){this.hostAriaMirror?.disconnect(),this.hostAriaMirror=new C(this,()=>this.getAriaMirrorTarget(),()=>this.syncStandaloneAria()),this.hostAriaMirror.connect()}syncHostAriaMirror(){this.hostAriaMirror?.sync()}formResetCallback(){this.resetValidity(),this.hasInteracted=!1,this.valueHasChanged=!1,this.emittedEvents=[],this.resetToDefaultValue(),this.syncFormValue(),this.updateValidity()}formDisabledCallback(e){this.disabled=e,this.updateValidity()}formStateRestoreCallback(e,t){this.restoreFormState(e),this.syncFormValue(),this.updateValidity()}set form(e){e?this.setAttribute(`form`,e):this.removeAttribute(`form`)}get form(){return this.internals.form}get labels(){return this.internals.labels}get validity(){return this.internals.validity}get willValidate(){return this.internals.willValidate}get validationMessage(){return this.internals.validationMessage}getForm(){return this.internals.form}checkValidity(){return this.updateValidity(),this.internals.checkValidity()}reportValidity(){return this.updateValidity(),this.hasInteracted=!0,this.internals.reportValidity()}resetValidity(){this.setCustomValidity(``),this.internals.setValidity({}),this.syncCustomStates()}setCustomValidity(e){if(!e){this.customError=null,this.internals.setValidity({}),this.syncCustomStates();return}this.customError=e;let t=this.validationTarget;t instanceof HTMLElement?this.internals.setValidity({customError:!0},e,t):this.internals.setValidity({customError:!0},e),this.syncCustomStates()}get validationTarget(){return this.input}get allValidators(){return[...this.constructor.validators??[],...this.validators??[]]}setFormValue(e,t){this.internals.setFormValue(e,t??e)}setValue(e,t){this.setFormValue(e,t??e)}updateValidity(){if(this.disabled||this.hasAttribute(`disabled`)||!this.willValidate){this.internals.setValidity({}),this.syncCustomStates();return}let e=this.allValidators;if(!e.length)return;let t={customError:!!this.customError},n=``,r=this.validationTarget;for(let r of e){let{isValid:e,message:i,invalidKeys:a}=r.checkValidity(this);if(!e){n||=i;for(let e of a)t[e]=!0}}n||=this.validationMessage,r instanceof HTMLElement?this.internals.setValidity(t,n,r):this.internals.setValidity(t,n),this.syncCustomStates()}syncCustomStates(){let e=this.internals.validity.valid;this.setState(`required`,this.required),this.setState(`optional`,!this.required),this.setState(`invalid`,!e),this.setState(`valid`,e),this.setState(`user-invalid`,!e&&this.hasInteracted),this.setState(`user-valid`,e&&this.hasInteracted)}setState(e,t){let n=this.internals.states;n&&(t?n.add(e):n.delete(e))}syncFormValue(){}resetToDefaultValue(){}restoreFormState(e){}};g([_({reflect:!0})],E.prototype,`name`,void 0),g([_({type:Boolean,reflect:!0})],E.prototype,`disabled`,void 0),g([_({type:Boolean,reflect:!0})],E.prototype,`required`,void 0),g([_({attribute:`custom-error`,reflect:!0})],E.prototype,`customError`,void 0),g([_({attribute:!1,state:!0})],E.prototype,`valueHasChanged`,void 0),g([_({attribute:!1,state:!0})],E.prototype,`hasInteracted`,void 0);function D(){return{checkValidity(e){let t=e.input,n={message:``,isValid:!0,invalidKeys:[]};if(!t)return n;let r=!0;if(`checkValidity`in t&&typeof t.checkValidity==`function`&&(r=t.checkValidity()),r)return n;if(n.isValid=!1,`validationMessage`in t&&typeof t.validationMessage==`string`&&(n.message=t.validationMessage),!(`validity`in t)||!t.validity)return n.invalidKeys.push(`customError`),n;for(let e of Object.keys(t.validity)){if(e===`valid`)continue;let r=e;t.validity[r]&&n.invalidKeys.push(r)}return n}}}function O(e={}){let{validationElement:t,validationProperty:n}=e;!t&&typeof document<`u`&&(t=Object.assign(document.createElement(`input`),{required:!0})),n||=`value`;let r={observedAttributes:[`required`],message:t?.validationMessage??`Please fill out this field.`,checkValidity(e){let t={message:``,isValid:!0,invalidKeys:[]};if(!e.required)return t;let i=e[n];return i!=null&&i!==!1&&i!==``||(t.isValid=!1,t.message=typeof r.message==`function`?r.message(e):r.message??``,t.invalidKeys.push(`valueMissing`)),t}};return r}var k=0;function A(e=`pk`){return k+=1,`${e}-${k}`}var j=class extends Event{constructor(){super(`pk-clear`,{bubbles:!0,cancelable:!1,composed:!0})}};function M(e,t){return t||(e.getAttribute(`hint`)??``)}function N(e,t,n=!1){return!!t||e(`instructions`,n)||e(`hint`)}var{I:ee}=n,P=e=>e.strings===void 0,F={},I=(e,t=F)=>e._$AH=t,L=p(class extends s{constructor(e){if(super(e),e.type!==c.PROPERTY&&e.type!==c.ATTRIBUTE&&e.type!==c.BOOLEAN_ATTRIBUTE)throw Error("The `live` directive is not allowed on child or event bindings");if(!P(e))throw Error("`live` bindings can only contain a single expression")}render(e){return e}update(t,[n]){if(n===h||n===e)return n;let r=t.element,i=t.name;if(t.type===c.PROPERTY){if(n===r[i])return h}else if(t.type===c.BOOLEAN_ATTRIBUTE){if(!!n===r.hasAttribute(i))return h}else if(t.type===c.ATTRIBUTE&&r.getAttribute(i)===n+``)return h;return I(t),n}}),R=t=>t??e,z=new Set([`button`,`submit`,`reset`,`checkbox`,`radio`,`file`,`image`,`hidden`]),B=`pk-implicit-submit`,V=(e,t)=>{if(e.key!==`Enter`||e.defaultPrevented||e.isComposing||e.altKey||e.ctrlKey||e.metaKey||e.shiftKey)return!1;let n=(t||`text`).toLowerCase();return!z.has(n)},H=e=>{let t=e.closest?.(`pk-dialog`);if(t){let e=t.querySelector(`form`);if(e)return e}let n=e.form;return n&&n.id===`main`?e.closest?.(`form`)===n?null:e.closest(`form`):n},U=(e,t,n)=>{if(e.disabled||e.readonly||!V(t,n))return!1;let r=H(e);return!r||r.id===`main`?!1:(t.preventDefault(),t.stopPropagation(),r.dispatchEvent(new CustomEvent(B,{bubbles:!1,cancelable:!0})),!0)},W=t`
    @layer pk-component {
        :host {
            display: block;
            width: 100%;
            font-family: var(--pk-font-family);
            font-size: var(--pk-control-font-default);
            line-height: var(--pk-line-height);
            /*
             * Control chrome tokens — inherit into light-DOM in-control actions
             * (e.g. pk-copy-button[slot=end]) the same way combobox/image-browser
             * size their trailing clear/expand hit targets.
             */
            --pk-input-padding-block: calc((var(--pk-control-height-default) - var(--pk-input-control-line-height, 1.25rem) - 2px) / 2);
            --pk-input-padding-inline: 8px;
            --pk-input-control-gap: 6px;
            --pk-input-decoration-size: 0.75rem;
        }

        :host([data-pk-group-orientation]) {
            display: flex;
            flex-direction: column;
            width: auto;
            flex: 0 1 auto;
            align-self: stretch;
        }

        :host([data-pk-group-orientation]) .form-control {
            gap: 0;
            height: 100%;
        }

        :host([data-pk-group-orientation]) .form-control__input {
            min-height: var(--pk-btn-height-default);
            height: 100%;
        }

        :host([data-pk-group-orientation]) .form-control__start,
        :host([data-pk-group-orientation]) .form-control__end {
            display: none;
        }

        :host([data-pk-group-orientation]) .form-control__input {
            width: 100%;
        }

        :host([data-pk-group-orientation]) .input {
            width: 100%;
        }

        :host([data-pk-group-orientation]) .input {
            min-height: var(--pk-btn-height-default);
            height: 100%;
        }

        :host([data-pk-group-orientation='vertical']) {
            width: 100%;
        }

        :host([data-pk-group-orientation='horizontal'][data-pk-group-join]:not([data-pk-group-divider])) {
            margin-inline-start: var(--pk-bg-horizontal-indent-outlined, 0);
        }

        :host([data-pk-group-orientation='vertical'][data-pk-group-join]:not([data-pk-group-divider])) {
            margin-block-start: var(--pk-bg-vertical-indent-outlined, 0);
        }

        :host([data-pk-group-orientation='horizontal'][data-pk-group-divider][data-pk-group-join]) {
            margin-inline-start: var(--pk-bg-horizontal-indent-outlined, 0);
        }

        :host([data-pk-group-orientation='vertical'][data-pk-group-divider][data-pk-group-join]) {
            margin-block-start: var(--pk-bg-vertical-indent-outlined, 0);
        }

        :host([data-pk-group-orientation='horizontal'][data-pk-group-divider]) .form-control__input {
            border-left-width: 1px;
            border-left-style: solid;
            border-left-color: var(--pk-btn-group-divider-color-outline, var(--pk-input-border-color));
            box-shadow: none;
        }

        :host([data-pk-group-orientation='vertical'][data-pk-group-divider]) .form-control__input {
            border-top-width: 1px;
            border-top-style: solid;
            border-top-color: var(--pk-btn-group-divider-color-outline, var(--pk-input-border-color));
            box-shadow: none;
        }

        :host([data-pk-group-divider]) .form-control__input:focus-within,
        :host([data-pk-group-divider][data-state='focus-visible']) .form-control__input {
            box-shadow: var(--pk-input-focus-shadow);
        }

        :host([data-pk-group-orientation='vertical'][data-pk-group-divider]) .form-control__input:focus-within,
        :host([data-pk-group-orientation='vertical'][data-pk-group-divider][data-state='focus-visible']) .form-control__input {
            box-shadow: var(--pk-input-focus-shadow);
        }

        /* Chrome lives on the flex shell (part=base) so slot=start/end adornments sit
         * inside the border — same visual contract as pk-input-group / v1 InputGroup.
         * Height is content-sized (v1): padding-block + --pk-input-control-line-height + border.
         * Trailing actions (clear, pk-copy-button[slot=end]) stay in flex flow so long
         * values never paint under the button — mirror combobox / image-browser.
         */
        .form-control__input {
            align-items: center;
            gap: var(--pk-input-control-gap);
            padding-inline: var(--pk-input-padding-inline);
            border: var(--pk-input-border);
            border-radius: var(--pk-input-border-radius, var(--pk-radius-sm));
            background: var(--pk-input-bg);
            background-clip: padding-box;
            box-sizing: border-box;
            transition: border-color 0.12s ease, box-shadow 0.12s ease;
        }

        .form-control__start,
        .form-control__end {
            margin: 0;
            color: var(--pk-color-gray-400);
            line-height: 0;
            align-self: stretch;
            align-items: center;
        }

        /* Decorative glyphs only — interactive in-control actions opt out below. */
        .form-control__start ::slotted(*),
        .form-control__end ::slotted(*) {
            display: block;
            max-width: 1.25rem;
            max-height: 1.25rem;
        }

        /* Copy (and similar) inside the field: flex-reserved space, not absolute overlay. */
        .form-control__end.has-action {
            align-items: stretch;
        }

        .form-control__end ::slotted(pk-copy-button) {
            display: inline-flex;
            align-self: stretch;
            align-items: stretch;
            max-width: none;
            max-height: none;
            /*
             * Pull into trailing padding like combobox expand/clear, but leave a
             * small inset so the glyph is not tight against the field border.
             */
            margin-inline-end: calc(-1 * var(--pk-input-padding-inline) + 4px);
            margin-block: calc(-1 * var(--pk-input-padding-block));
        }

        .input {
            display: block;
            width: 100%;
            margin: 0;
            /* v1 Input default: py-1.5 + text-sm (14px / 1.25rem lh) → 34px with border. */
            padding-block: var(--pk-input-padding-block);
            padding-inline: 0;
            border: 0;
            border-radius: 0;
            background: transparent;
            /* Craft CP body / field value text. */
            color: var(--pk-color-gray-700);
            font: inherit;
            line-height: var(--pk-input-control-line-height, 1.25rem);
            appearance: none;
            box-sizing: border-box;
            outline: none;
        }

        .form-control__input .input {
            flex: 1 1 auto;
            min-width: 0;
        }

        /* Keep the browser from adding a second clear control. */
        .input[data-custom-clear]::-webkit-search-cancel-button {
            -webkit-appearance: none;
            display: none;
        }

        .input::placeholder {
            color: var(--pk-input-placeholder-color, var(--pk-color-gray-400));
        }

        /*
         * Craft text:focus-visible only sets box-shadow (--focus-ring); resting border stays.
         * Do not also set border-color — --pk-input-focus-shadow already includes 0 0 0 1px,
         * so border-color + that ring reads as a double focus treatment.
         */
        :host(:not([invalid]):not(:state(user-invalid))) .form-control__input:focus-within,
        :host([data-state='focus-visible']:not([invalid]):not(:state(user-invalid))) .form-control__input {
            box-shadow: var(--pk-input-focus-shadow);
        }

        .form-control__input:has(.input:disabled) {
            cursor: not-allowed;
            opacity: 0.5;
        }

        .input:disabled {
            cursor: not-allowed;
        }

        :host([invalid]) .form-control__input,
        :host(:state(user-invalid)) .form-control__input {
            border-color: var(--pk-color-rose-600);
        }

        /* Invalid + focus: rose ring (same token as select/combobox), not sky over rose border. */
        :host([invalid]) .form-control__input:focus-within,
        :host([invalid][data-state='focus-visible']) .form-control__input,
        :host(:state(user-invalid)) .form-control__input:focus-within {
            box-shadow: var(--pk-input-invalid-focus-shadow);
        }

        :host([size='xxs']) {
            --pk-input-padding-block: calc((var(--pk-control-height-xxs) - var(--pk-input-control-line-height, 1.25rem) - 2px) / 2);
            --pk-input-padding-inline: 6px;
            --pk-input-control-gap: 4px;
            --pk-input-decoration-size: 0.625rem;
        }

        :host([size='xs']) {
            --pk-input-padding-block: calc((var(--pk-control-height-xs) - var(--pk-input-control-line-height, 1.25rem) - 2px) / 2);
            --pk-input-padding-inline: 6px;
            --pk-input-control-gap: 4px;
            --pk-input-decoration-size: 0.625rem;
        }

        :host([size='xxs']) .input {
            font-size: var(--pk-control-font-xxs);
        }

        :host([size='xs']) .input {
            font-size: var(--pk-control-font-xs);
        }

        :host([size='sm']) {
            --pk-input-padding-block: calc((var(--pk-control-height-sm) - var(--pk-input-control-line-height, 1.25rem) - 2px) / 2);
            --pk-input-padding-inline: 8px;
            --pk-input-control-gap: 4px;
            --pk-input-decoration-size: 0.6875rem;
        }

        :host([size='sm']) .input {
            font-size: var(--pk-control-font-sm);
        }

        :host([size='lg']) {
            --pk-input-padding-block: calc((var(--pk-control-height-lg) - var(--pk-input-control-line-height, 1.25rem) - 2px) / 2);
            --pk-input-padding-inline: 12px;
            --pk-input-control-gap: 8px;
            --pk-input-decoration-size: 0.875rem;
        }

        :host([size='lg']) .input {
            font-size: var(--pk-control-font-lg);
        }

        :host([size='xl']) {
            --pk-input-padding-block: calc((var(--pk-control-height-xl) - var(--pk-input-control-line-height, 1.25rem) - 2px) / 2);
            --pk-input-padding-inline: 16px;
            --pk-input-control-gap: 8px;
            --pk-input-decoration-size: 1rem;
        }

        :host([size='xl']) .input {
            font-size: var(--pk-control-font-xl);
        }

        /* Optical font sizing must not change the control height. */
        :host([mono]) .input {
            font-family: var(--pk-input-mono-font-family);
            font-size: calc(var(--pk-control-font-default) * 0.9);
            line-height: var(--pk-input-control-line-height, 1.25rem);
        }

        :host([mono][size='xxs']) .input {
            font-size: calc(var(--pk-control-font-xxs) * 0.9);
        }

        :host([mono][size='xs']) .input {
            font-size: calc(var(--pk-control-font-xs) * 0.9);
        }

        :host([mono][size='sm']) .input {
            font-size: calc(var(--pk-control-font-sm) * 0.9);
        }

        :host([mono][size='lg']) .input {
            font-size: calc(var(--pk-control-font-lg) * 0.9);
        }

        :host([mono][size='xl']) .input {
            font-size: calc(var(--pk-control-font-xl) * 0.9);
        }

        /* Editable-table cells (v1): flush into the row — no chrome border/radius.
         * Prefer reflected fit-cell (Lit property); data-editable-table-input is a legacy alias.
         * Fill host → form-control → input so the control spans the full td.
         */
        :host([fit-cell]),
        :host([data-editable-table-input]) {
            display: block;
            height: 100%;
            min-height: 100%;
            box-sizing: border-box;
        }

        :host([fit-cell]) .form-control,
        :host([data-editable-table-input]) .form-control {
            height: 100%;
            min-height: 100%;
            gap: 0;
        }

        :host([fit-cell]) .form-control__input,
        :host([data-editable-table-input]) .form-control__input {
            height: 100%;
            min-height: 100%;
            flex: 1 1 auto;
            padding-inline: 0;
            border: none;
            border-radius: 0;
            background: transparent;
            box-shadow: none;
        }

        :host([fit-cell]) .input,
        :host([data-editable-table-input]) .input {
            height: 100%;
            min-height: 100%;
        }

        :host([fit-cell]:not([invalid]):not(:state(user-invalid))) .form-control__input:focus-within,
        :host([fit-cell][data-state='focus-visible']:not([invalid]):not(:state(user-invalid))) .form-control__input,
        :host([data-editable-table-input]:not([invalid]):not(:state(user-invalid))) .form-control__input:focus-within,
        :host([data-editable-table-input][data-state='focus-visible']:not([invalid]):not(:state(user-invalid))) .form-control__input {
            border: none;
            box-shadow: inset 0 0 0 1px var(--pk-color-gray-200);
        }

        :host([fit-cell][invalid]) .form-control__input,
        :host([fit-cell]:state(user-invalid)) .form-control__input,
        :host([data-editable-table-input][invalid]) .form-control__input,
        :host([data-editable-table-input]:state(user-invalid)) .form-control__input {
            border: none;
            box-shadow: inset 0 0 0 1px var(--pk-color-rose-600);
        }

        :host([fit-cell][invalid]) .form-control__input:focus-within,
        :host([fit-cell][invalid][data-state='focus-visible']) .form-control__input,
        :host([fit-cell]:state(user-invalid)) .form-control__input:focus-within,
        :host([data-editable-table-input][invalid]) .form-control__input:focus-within,
        :host([data-editable-table-input][invalid][data-state='focus-visible']) .form-control__input,
        :host([data-editable-table-input]:state(user-invalid)) .form-control__input:focus-within {
            border: none;
            box-shadow: inset 0 0 0 1px var(--pk-color-rose-600);
        }

        :host([data-pk-group-orientation='horizontal'][data-pk-group-join]:not([data-pk-group-divider])) .form-control__input {
            border-left-width: 0;
        }

        :host([data-pk-group-orientation='vertical'][data-pk-group-join]:not([data-pk-group-divider])) .form-control__input {
            border-top-width: 0;
        }

        :host([data-pk-group-orientation='horizontal'][data-pk-group-divider]) .form-control__input {
            border-left-width: 1px;
            border-left-style: solid;
            border-left-color: var(--pk-btn-group-divider-color-outline, var(--pk-input-border-color));
        }

        :host([data-pk-group-orientation='vertical'][data-pk-group-divider]) .form-control__input {
            border-top-width: 1px;
            border-top-style: solid;
            border-top-color: var(--pk-btn-group-divider-color-outline, var(--pk-input-border-color));
        }

        :host([data-pk-group-orientation='horizontal'][data-pk-group-internal-trail]) .form-control__input {
            border-right-width: 0;
        }

        :host([data-pk-group-orientation='vertical'][data-pk-group-internal-trail]) .form-control__input {
            border-bottom-width: 0;
        }

        /*
         * Clear is a flex trailing action (not absolute). Reserves width in the
         * control so values cannot scroll under the glyph — same contract as
         * combobox clear/expand and image-browser clear.
         */
        .clear-button {
            display: inline-flex;
            flex-shrink: 0;
            align-self: stretch;
            align-items: center;
            justify-content: center;
            box-sizing: border-box;
            width: calc(var(--pk-input-decoration-size) + var(--pk-input-padding-inline));
            margin-block: calc(-1 * var(--pk-input-padding-block));
            /* Match pk-copy-button[slot=end]: pull into padding but leave a 4px glyph inset. */
            margin-inline-end: calc(-1 * var(--pk-input-padding-inline) + 4px);
            font-size: var(--pk-input-decoration-size);
            line-height: 1;
        }
    }
`,G=class extends E{constructor(...e){super(...e),this.assumeInteractionOn=[`blur`,`input`],this.hasSlotController=new l(this,`instructions`,`hint`,`label`,`start`,`end`),this.inputId=A(`pk-input`),this.type=`text`,this._value=null,this.defaultValue=null,this.size=`default`,this.label=``,this.instructions=``,this.withClear=!1,this.clearLabel=`Clear`,this.placeholder=``,this.readonly=!1,this.invalid=!1,this.fitCell=!1,this.mono=!1,this.autofocus=!1,this.withLabel=!1,this.withInstructions=!1}static{this.styles=[v,u(),d(`.input`,`var(--pk-input-border-radius, var(--pk-radius-sm))`),f(`.input`),W]}static get validators(){return[...super.validators,D(),O()]}get value(){return this.valueHasChanged?this._value??``:this._value??this.defaultValue??``}set value(e){let t=e??``;this._value!==t&&(this.valueHasChanged=!0,this._value=t)}connectedCallback(){this.instructions=M(this,this.instructions),this.hasAttribute(`with-hint`)&&(this.withInstructions=!0),super.connectedCallback()}syncFormValue(){this.setValue(this.value||``)}resetToDefaultValue(){this.valueHasChanged=!1,this._value=null}restoreFormState(e){typeof e==`string`&&(this.value=e)}formResetCallback(){this.valueHasChanged=!1,this._value=null,this.input&&(this.input.value=this.defaultValue??``),super.formResetCallback()}updated(e){(e.has(`value`)||e.has(`defaultValue`))&&this.setState(`blank`,!this.value),super.updated(e)}syncStandaloneAria(){if(!this.input)return;let e=!!this.label||this.hasSlotController.test(`label`,this.withLabel),t=N((e,t)=>this.hasSlotController.test(e,t),this.instructions,this.withInstructions);S({control:this.input,labelId:`${this.inputId}-label`,instructionsId:`${this.inputId}-instructions`,hasLabel:e,hasInstructions:t,required:this.required,invalid:this.invalid||!this.internals.validity.valid})}hasLabelContent(){return!!this.label||this.hasSlotController.test(`label`,this.withLabel)}hasInstructionsContent(){return N((e,t)=>this.hasSlotController.test(e,t),this.instructions,this.withInstructions)}focus(e){this.input?.focus(e)}blur(){this.input?.blur()}select(){this.input?.select()}handleInput(){this.value=this.input.value,this.dispatchEvent(new Event(`input`,{bubbles:!0,composed:!0}))}handleChange(e){this.value=this.input.value,e.stopPropagation(),this.dispatchEvent(new Event(`change`,{bubbles:!0,composed:!0}))}handleKeyDown(e){U(this,e,this.type)}handleClearClick(e){e.preventDefault(),this.value!==``&&(this.value=``,this.dispatchEvent(new j),this.dispatchEvent(new Event(`input`,{bubbles:!0,composed:!0})),this.dispatchEvent(new Event(`change`,{bubbles:!0,composed:!0})),this.input.focus())}endHasAction(){return[...this.children].some(e=>e.getAttribute(`slot`)===`end`&&e.localName===`pk-copy-button`)}handleEndSlotChange(){this.requestUpdate()}render(){let t=this.hasLabelContent(),n=this.hasInstructionsContent(),r=this.withClear&&!this.disabled&&!this.readonly&&this.value.length>0,a=this.hasSlotController.test(`start`),o=this.hasSlotController.test(`end`);return i`
            <div part="form-control" class="form-control">
                ${t||n?i`
                        <div part="header" class="form-control__header">
                            ${t?i`
                                    <label
                                        part="label"
                                        class="form-control__label"
                                        id=${`${this.inputId}-label`}
                                        for=${`${this.inputId}-control`}
                                    >
                                        <slot name="label">${this.label}</slot>
                                    </label>
                                `:e}

                            ${n?i`
                                    <p
                                        part="instructions"
                                        class="form-control__instructions"
                                        id=${`${this.inputId}-instructions`}
                                    >
                                        <slot name="instructions">${this.instructions}</slot>
                                        <slot name="hint"></slot>
                                    </p>
                                `:e}
                        </div>
                    `:e}

                <div part="base" class="form-control__input">
                    ${a?i`
                            <span part="start" class="form-control__start">
                                <slot name="start"></slot>
                            </span>
                        `:i`<slot name="start" hidden></slot>`}

                    <input
                        part="input"
                        class="input"
                        ?data-custom-clear=${this.withClear}
                        id=${t?`${this.inputId}-control`:e}
                        type=${this.type}
                        .value=${L(this.value)}
                        placeholder=${this.placeholder||e}
                        pattern=${R(this.pattern)}
                        minlength=${R(this.minlength)}
                        maxlength=${R(this.maxlength)}
                        min=${R(this.min)}
                        max=${R(this.max)}
                        step=${R(this.step)}
                        autocomplete=${R(this.autocomplete)}
                        ?disabled=${this.disabled}
                        ?readonly=${this.readonly}
                        ?required=${this.required}
                        ?autofocus=${this.autofocus}
                        @input=${this.handleInput}
                        @change=${this.handleChange}
                        @keydown=${this.handleKeyDown}
                        @focus=${()=>this.dispatchEvent(new Event(`focus`,{bubbles:!0,composed:!0}))}
                        @blur=${()=>this.dispatchEvent(new Event(`blur`,{bubbles:!0,composed:!0}))}
                    />

                    ${r?i`
                            <button
                                part="clear-button"
                                class="icon-button clear-button"
                                type="button"
                                aria-label=${this.clearLabel}
                                @click=${this.handleClearClick}
                            >
                                <slot name="clear-icon">×</slot>
                            </button>
                        `:e}

                    ${o?i`
                            <span part="end" class=${this.endHasAction()?`form-control__end has-action`:`form-control__end`}>
                                <slot name="end" @slotchange=${this.handleEndSlotChange}></slot>
                            </span>
                        `:i`<slot name="end" hidden></slot>`}
                </div>
            </div>
        `}};g([a(`input`)],G.prototype,`input`,void 0),g([_({reflect:!0})],G.prototype,`type`,void 0),g([m()],G.prototype,`value`,null),g([_({attribute:`value`,reflect:!0})],G.prototype,`defaultValue`,void 0),g([_({reflect:!0})],G.prototype,`size`,void 0),g([_()],G.prototype,`label`,void 0),g([_()],G.prototype,`instructions`,void 0),g([_({attribute:`with-clear`,type:Boolean})],G.prototype,`withClear`,void 0),g([_({attribute:`clear-label`})],G.prototype,`clearLabel`,void 0),g([_()],G.prototype,`placeholder`,void 0),g([_({type:Boolean,reflect:!0})],G.prototype,`readonly`,void 0),g([_({type:Boolean,reflect:!0})],G.prototype,`invalid`,void 0),g([_({type:Boolean,reflect:!0,attribute:`fit-cell`})],G.prototype,`fitCell`,void 0),g([_({type:Boolean,reflect:!0})],G.prototype,`mono`,void 0),g([_()],G.prototype,`pattern`,void 0),g([_({type:Number})],G.prototype,`minlength`,void 0),g([_({type:Number})],G.prototype,`maxlength`,void 0),g([_()],G.prototype,`min`,void 0),g([_()],G.prototype,`max`,void 0),g([_()],G.prototype,`step`,void 0),g([_()],G.prototype,`autocomplete`,void 0),g([_({type:Boolean,reflect:!0})],G.prototype,`autofocus`,void 0),g([_({attribute:`with-label`,type:Boolean})],G.prototype,`withLabel`,void 0),g([_({attribute:`with-instructions`,type:Boolean})],G.prototype,`withInstructions`,void 0),G=g([r(`pk-input`)],G);var K=t`
    .sr-only {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border: 0;
    }
`,q=t`
    @layer pk-component {
        :host {
            display: block;
            container-type: inline-size;
            font-family: var(--pk-font-family);
            font-size: var(--pk-font-size-base);
            line-height: var(--pk-line-height);
            color: var(--pk-color-gray-800);
            --pk-setup-checklist-padding: 1.5rem;
            --pk-setup-checklist-accent: var(--pk-color-gray-800);
            --pk-setup-checklist-done: var(--pk-color-emerald-600);
            --pk-setup-checklist-current-background: var(--pk-color-gray-50);
            --pk-setup-checklist-card-background: var(--pk-color-white);
            --pk-setup-checklist-card-shadow:
                0 0 0 1px color-mix(in srgb, var(--pk-color-gray-900) 10%, transparent),
                0 2px 12px -4px color-mix(in srgb, var(--pk-color-gray-900) 10%, transparent);
            --pk-setup-checklist-aside-width: 15rem;
        }

        .base {
            display: grid;
            grid-template-columns: minmax(0, 1fr);
            gap: 1.5rem;
            align-items: start;
        }

        @container (min-width: 48rem) {
            .base[data-aside] {
                grid-template-columns: minmax(0, 2fr) minmax(var(--pk-setup-checklist-aside-width), 1fr);
            }
        }

        .card {
            min-width: 0;
            box-sizing: border-box;
            padding: 1.25rem var(--pk-setup-checklist-padding);
            border-radius: var(--pk-radius-lg);
            background: var(--pk-setup-checklist-card-background);
            box-shadow: var(--pk-setup-checklist-card-shadow);
        }

        /* The last step's row sits flush with the card's bottom edge, so its highlight reaches the corners. */
        .checklist {
            padding-bottom: 0;
            overflow: hidden;
        }

        .header {
            display: flex;
            gap: 0.75rem;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 1rem;
        }

        .header-text {
            min-width: 0;
        }

        .heading {
            margin: 0;
            font-size: 1rem;
            font-weight: 700;
            line-height: 1.375;
        }

        .intro {
            margin-top: 0.25rem;
            color: var(--pk-color-gray-500);
        }

        .intro ::slotted(*) {
            margin: 0;
        }

        .count {
            flex: none;
            padding-top: 0.125rem;
            color: var(--pk-color-gray-500);
            font-size: var(--pk-font-size-sm);
            white-space: nowrap;
        }

        .steps {
            display: block;
            margin-inline: calc(-1 * var(--pk-setup-checklist-padding));
        }

        /* Inside a pane that is already a card, the checklist and aside drop their own cards. The steps still reach past
           the content by the padding, so the current step's highlight runs to the pane's edges. */
        :host([appearance='plain']) .card {
            padding: 0;
            border-radius: 0;
            background: none;
            box-shadow: none;
        }

        :host([appearance='plain']) .checklist {
            overflow: visible;
        }
    }
`,J=[K,t`
        @layer pk-component {
            :host {
                display: block;
                container-type: inline-size;
                padding-inline: var(--pk-setup-checklist-padding, 1.5rem);
                font-family: var(--pk-font-family);
                font-size: var(--pk-font-size-base);
                line-height: var(--pk-line-height);
                color: var(--pk-color-gray-800);
            }

            :host([current]) {
                background: var(--pk-setup-checklist-current-background, var(--pk-color-gray-50));
            }

            /* The marker and action line up with the heading's first line, so a tall step, such as one holding starter
               cards, keeps its number beside its heading. */
            .base {
                --_heading-line: calc(var(--pk-font-size-base) * var(--pk-line-height));
                display: grid;
                grid-template-columns: 1.5rem minmax(0, 1fr) auto;
                gap: 0.25rem 0.75rem;
                align-items: start;
                padding-block: 0.875rem;
                border-top: 1px solid color-mix(in srgb, var(--pk-color-gray-900) 10%, transparent);
            }

            .marker {
                display: grid;
                place-items: center;
                width: 1.5rem;
                height: 1.5rem;
                border-radius: 50%;
                background: var(--pk-color-gray-100);
                color: var(--pk-color-gray-600);
                margin-top: calc((var(--_heading-line) - 1.5rem) / 2);
                font-size: 0.75rem;
                font-weight: 600;
                font-variant-numeric: tabular-nums;
            }

            .marker svg {
                width: 0.875rem;
                height: 0.875rem;
                fill: none;
                stroke: currentColor;
                stroke-width: 3;
                stroke-linecap: round;
                stroke-linejoin: round;
            }

            :host([current]) .marker {
                background: var(--pk-setup-checklist-accent, var(--pk-color-gray-800));
                color: var(--pk-color-white);
            }

            :host([done]) .marker {
                background: var(--pk-setup-checklist-done, var(--pk-color-emerald-600));
                color: var(--pk-color-white);
            }

            .body {
                min-width: 0;
            }

            .heading {
                font-weight: 600;
            }

            :host([done]) .heading-text {
                color: var(--pk-color-gray-500);
                text-decoration: line-through;
            }

            .description {
                margin-top: 0.125rem;
                color: var(--pk-color-gray-500);
            }

            .description ::slotted(*) {
                margin: 0;
            }

            .snippet {
                margin-top: 0.625rem;
            }

            .snippet pk-input {
                display: block;
            }

            /* Matches a read-only pk-input, with room on the right for the copy button in the corner. */
            .snippet--block {
                position: relative;
            }

            pre {
                box-sizing: border-box;
                margin: 0;
                padding: 0.5rem 2.5rem 0.5rem 0.75rem;
                overflow-x: auto;
                border: 1px solid var(--pk-color-gray-200);
                border-radius: var(--pk-radius-md);
                background: var(--pk-color-white);
                color: var(--pk-color-gray-800);
                font-family: var(--pk-font-family-mono);
                font-size: var(--pk-font-size-mono);
                line-height: 1.5;
                white-space: pre;
            }

            .snippet--block .copy {
                position: absolute;
                top: 0.25rem;
                right: 0.25rem;
            }

            .actions {
                display: flex;
                flex-wrap: wrap;
                gap: 0.5rem;
                justify-content: flex-end;
            }

            /* In a narrow card the action drops under the description instead of squeezing it. */
            @container (max-width: 30rem) {
                .base {
                    grid-template-columns: 1.5rem minmax(0, 1fr);
                }

                .actions {
                    grid-column: 2;
                    justify-content: flex-start;
                }
            }
        }
    `],Y=i`<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12.5l4.5 4.5L19 7.5"></path></svg>`,X=class extends o{constructor(...e){super(...e),this.hasSlotController=new l(this,`action`,`change`),this.heading=``,this.done=!1,this.snippet=``,this.position=0,this.current=!1,this.headingLevel=3,this.doneLabel=`(done)`,this.copyLabel=`Copy`,this.copiedLabel=`Copied`,this.handleActionSlotChange=()=>{this.dispatchEvent(new Event(`pk-setup-step-update`,{bubbles:!0,composed:!1}))}}static{this.styles=J}connectedCallback(){super.connectedCallback(),this.hasAttribute(`role`)||this.setAttribute(`role`,`listitem`)}updated(e){e.has(`current`)&&(this.current?this.setAttribute(`aria-current`,`step`):this.removeAttribute(`aria-current`)),e.has(`done`)&&this.dispatchEvent(new Event(`pk-setup-step-update`,{bubbles:!0,composed:!1}))}renderSnippet(){if(this.done||!this.snippet)return e;let t=t=>i`<pk-copy-button
            slot=${t??e}
            class="copy"
            .value=${this.snippet}
            aria-label=${this.copyLabel}
            copied-label=${this.copiedLabel}
        ></pk-copy-button>`;return this.snippet.includes(`
`)?i`
            <div part="snippet" class="snippet snippet--block">
                <pre aria-label=${this.heading}><code>${this.snippet}</code></pre>
                ${t()}
            </div>
        `:i`
                <div part="snippet" class="snippet">
                    <pk-input readonly mono .value=${this.snippet} aria-label=${this.heading}>${t(`end`)}</pk-input>
                </div>
            `}renderActions(){let t=this.done?`change`:`action`;return this.hasSlotController.test(t)?i`<div part="actions" class="actions"><slot name=${t} @slotchange=${this.handleActionSlotChange}></slot></div>`:e}render(){return i`
            <div part="base" class="base">
                <span part="marker" class="marker" aria-hidden="true">${this.done?Y:this.position||e}</span>
                <div part="body" class="body">
                    <div part="heading" class="heading" role="heading" aria-level=${this.headingLevel}>
                        <span class="heading-text">${this.heading}</span>${this.done?i`<span class="sr-only"> ${this.doneLabel}</span>`:e}
                    </div>
                    <div part="description" class="description"><slot></slot></div>
                    ${this.renderSnippet()}
                </div>
                ${this.renderActions()}
            </div>
        `}};g([_()],X.prototype,`heading`,void 0),g([_({type:Boolean,reflect:!0})],X.prototype,`done`,void 0),g([_()],X.prototype,`snippet`,void 0),g([_({type:Number,reflect:!0})],X.prototype,`position`,void 0),g([_({type:Boolean,reflect:!0})],X.prototype,`current`,void 0),g([_({type:Number,attribute:`heading-level`})],X.prototype,`headingLevel`,void 0),g([_({attribute:`done-label`})],X.prototype,`doneLabel`,void 0),g([_({attribute:`copy-label`})],X.prototype,`copyLabel`,void 0),g([_({attribute:`copied-label`})],X.prototype,`copiedLabel`,void 0),X=g([r(`pk-setup-step`)],X);var Z=`data-pk-setup-primary`;function Q(e){let t=`pk-button, .btn, button`;return e.matches(t)?e:e.querySelector(t)}var $=class extends o{constructor(...e){super(...e),this.hasSlotController=new l(this,`intro`,`aside`),this.headingId=A(`pk-setup-checklist-heading`),this.heading=``,this.headingLevel=2,this.countLabel=`{done} of {total} done`,this.doneLabel=`(done)`,this.copyLabel=`Copy`,this.copiedLabel=`Copied`,this.primaryAction=`current`,this.primaryClass=`submit`,this.appearance=`card`,this.steps=[],this.handleStepUpdate=()=>{this.requestUpdate()}}static{this.styles=q}get doneCount(){return this.steps.filter(e=>e.done).length}get currentPosition(){return this.steps.findIndex(e=>!e.done)+1}connectedCallback(){super.connectedCallback(),this.addEventListener(`pk-setup-step-update`,this.handleStepUpdate),this.collectSteps()}disconnectedCallback(){this.removeEventListener(`pk-setup-step-update`,this.handleStepUpdate),super.disconnectedCallback()}collectSteps(){this.steps=[...this.children].filter(e=>e instanceof X)}willUpdate(){let e=this.currentPosition,t=Math.min(this.headingLevel+1,6);this.steps.forEach((n,r)=>{n.position=r+1,n.current=r+1===e,n.headingLevel=t,n.doneLabel=this.doneLabel,n.copyLabel=this.copyLabel,n.copiedLabel=this.copiedLabel})}updated(){this.syncPrimaryAction()}syncPrimaryAction(){this.steps.forEach(e=>{let t=this.primaryAction===`current`&&e.current&&!e.done;for(let n of e.querySelectorAll(`:scope > [slot="action"]`)){let e=Q(n);if(!e)continue;let r=e.hasAttribute(Z);if(t&&!r)e.localName===`pk-button`?(e.setAttribute(Z,e.getAttribute(`variant`)??``),e.setAttribute(`variant`,`primary`)):e.classList.contains(this.primaryClass)||(e.setAttribute(Z,``),e.classList.add(this.primaryClass));else if(!t&&r){if(e.localName===`pk-button`){let t=e.getAttribute(Z);t?e.setAttribute(`variant`,t):e.removeAttribute(`variant`)}else e.classList.remove(this.primaryClass);e.removeAttribute(Z)}}})}renderCount(){if(!this.steps.length)return e;let t=this.countLabel.replaceAll(`{done}`,String(this.doneCount)).replaceAll(`{total}`,String(this.steps.length));return i`<span part="count" class="count">${t}</span>`}render(){let t=this.hasSlotController.test(`aside`);return i`
            <div part="base" class="base" ?data-aside=${t}>
                <section part="card" class="card checklist" aria-labelledby=${this.headingId}>
                    <div part="header" class="header">
                        <div class="header-text">
                            <div part="heading" class="heading" id=${this.headingId} role="heading" aria-level=${this.headingLevel}>${this.heading}</div>
                            ${this.hasSlotController.test(`intro`)?i`<div part="intro" class="intro"><slot name="intro"></slot></div>`:e}
                        </div>
                        ${this.renderCount()}
                    </div>
                    <div part="steps" class="steps" role="list" aria-labelledby=${this.headingId}>
                        <slot @slotchange=${this.collectSteps}></slot>
                    </div>
                </section>
                ${t?i`<div part="aside" class="card aside"><slot name="aside"></slot></div>`:e}
            </div>
        `}};g([_()],$.prototype,`heading`,void 0),g([_({type:Number,attribute:`heading-level`})],$.prototype,`headingLevel`,void 0),g([_({attribute:`count-label`})],$.prototype,`countLabel`,void 0),g([_({attribute:`done-label`})],$.prototype,`doneLabel`,void 0),g([_({attribute:`copy-label`})],$.prototype,`copyLabel`,void 0),g([_({attribute:`copied-label`})],$.prototype,`copiedLabel`,void 0),g([_({attribute:`primary-action`,reflect:!0})],$.prototype,`primaryAction`,void 0),g([_({attribute:`primary-class`})],$.prototype,`primaryClass`,void 0),g([_({reflect:!0})],$.prototype,`appearance`,void 0),g([m()],$.prototype,`steps`,void 0),$=g([r(`pk-setup-checklist`)],$);export{$ as PkSetupChecklist,X as PkSetupStep};