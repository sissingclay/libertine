/**
 * Created by Gosia on 13/06/2015.
 */

module Libertine {

    export class SelectElement {

        constructor (public element?: any) {
            this.selector(element);
        }

        selector (selector) {
            this.element    = document.querySelectorAll(selector);
            return this.element;
        }
    }

    export class Actions {

        constructor (public element?: any, public class_name?: string, private getSelector?: any) {
            this.getSelector    = new SelectElement();
        }

        selector (element:any) {
            this.element = this.getSelector.selector(element);
            return this;
        }

        hasClass () {
            if (this.class_name) {
                return this.element.className.match(new RegExp('(\\s|^)' + this.class_name + '(\\s|$)'));
            }
            return false;
        }

        setClass (class_name) {
            this.class_name = class_name;
        }

        addClass (class_name) {

            this.setClass(class_name);

            if (!this.hasClass()) {
                this.element.className += " " + this.class_name;
            }
            return this;
        }

        removeClass (class_name) {

            this.setClass(class_name);

            if (this.hasClass()) {
                var reg = new RegExp('(\\s|^)' + this.class_name + '(\\s|$)');
                this.element.className = this.element.className.replace(reg, ' ');
            }
            return this;
        }

        toggleClass (class_name) {

            this.setClass(class_name);
            this.loopNodes('toggleClass');

            if(this.hasClass()) {
                this.removeClass(this.class_name);
            } else {
                this.addClass(this.class_name);
            }

            return this;
        }

        loopNodes (action) {
            var myNodeList = this.element;
            for (var i = 0; i < myNodeList.length; i++) {
                console.log('myNodeList[i]', myNodeList[i]);
            }
        }
    }

    export class Event {

        constructor (public event?: any, private getSelector?: any) {
            this.getSelector    = new SelectElement();
        }

        click (action, callback) {

        }
    }
}

var lb          = {
    select: new Libertine.Actions()
};

document.querySelector('.lb-mobile-menu').addEventListener('click', function (ele) {
    ele.preventDefault();
    lb.select.selector('.lb-menu').toggleClass('active');
});