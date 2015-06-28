/**
 * Created by Clay on 13/06/2015.
 */

module Libertine {

    "use strict";

    export class $ {

        $: any;
        elements: any;

        constructor () {
            this.$ = function (selector) {
                if ('querySelectorAll' in document ) {
                    if (typeof selector === 'string') {
                        this.elements = document.querySelectorAll(selector);
                    } else {
                        this.elements = selector;
                    }

                    return this;
                } else {
                    return;
                }
            }
        }

        public on (name: string, callback: any) {
            new OnEvents(name, callback, this.elements);
            return this;
        }

        public toggleClass (className: string) {
            new OnEvents(className, null, this.elements, 'toggleClass');
            return this;
        }

        public hasClass (className: string) {
            new OnEvents(className, null, this.elements, 'hasClass');
            return this;
        }

        public child (name) {
            this.elements = this.elements.querySelectorAll(name);
            return this;
        }
    }

    export class OnEvents {

        name:string;

        constructor(name:string, callback:any, elements:any, action?: string) {
            this.loopNodes(name, callback, elements, action);
        }

        loopNodes(name:string, callback:any, elements:any, action?: string) {

            var myNodeList = elements;

            for (var i = 0; i < myNodeList.length; i++) {

                switch (action) {

                    case 'toggleClass' :
                        new ClassActions(name,myNodeList[i]).toggleClass();
                    break;

                    case 'toggleClass' :
                        new ClassActions(name,myNodeList[i]).hasClass();
                        break;

                    default :
                        if ('addEventListener' in window ) {
                            myNodeList[i].addEventListener(name, callback);
                        } else {
                            //fallback to be added here
                            myNodeList[i].attachEvent('onclick', callback);
                        }
                    break;
                }
            }
        }
    }

    export class ClassActions {

        constructor (public class_name: string, public element: any) {
            this.class_name = class_name;
            this.element    = element;
        }

        hasClass () {
            if (this.class_name) {
                return this.element.className.match(new RegExp('(\\s|^)' + this.class_name + '(\\s|$)'));
            }
            return false;
        }

        addClass () {

            if (!this.hasClass()) {
                this.element.className += " " + this.class_name;
            }
            return this;
        }

        removeClass () {

            if (this.hasClass()) {
                var reg = new RegExp('(\\s|^)' + this.class_name + '(\\s|$)');
                this.element.className = this.element.className.replace(reg, ' ');
            }
            return this;
        }

        toggleClass () {

            if (this.hasClass()) {
                this.removeClass();
            } else {
                this.addClass();
            }
        }
    }
}

var lb = new Libertine.$();

lb.$('.lb-mobile-menu').on('click', function(ele) {
    ele.preventDefault();
    lb.$('.lb-menu').toggleClass('active');
});

//lb.$('.lb-menu-link').on('click', function(ele) {
//
//    var sub_menu = lb.$(this).child('.submenu');
//
//    if (sub_menu) {
//        ele.preventDefault();
//        sub_menu.toggleClass('active');
//    }
//});