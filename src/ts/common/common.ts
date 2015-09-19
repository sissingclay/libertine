/**
 * Created by Clay on 13/06/2015.
 */

module Libertine {

    "use strict";

    export class $ {

        elements: any;

        constructor (selector) {

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

            switch (action) {

                case 'toggleClass' :

                    if(myNodeList.length) {
                        for (var i = 0; i < myNodeList.length; i++) {
                            new ClassActions(name, myNodeList[i]).toggleClass();
                        }

                        return;
                    }

                    new ClassActions(name, myNodeList).toggleClass();

                break;

                case 'hasClass' :
                    for (var i = 0; i < myNodeList.length; i++) {
                        new ClassActions(name, myNodeList[i]).hasClass();
                    }
                    break;

                default :
                    for (var i = 0; i < myNodeList.length; i++) {
                        if ('addEventListener' in window) {
                            myNodeList[i].addEventListener(name, callback);
                        } else {
                            //fallback to be added here
                            myNodeList[i].attachEvent('on' + name, callback);
                        }
                    }
                break;
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

var lb = function(element) {
    return new Libertine.$(element);
}

document.addEventListener('DOMContentLoaded', function () {

    lb('.lb-head-mobile-menu').on('click', function(ele) {
        ele.preventDefault();
        lb('.lb-head-menu').toggleClass('active');
    });

    lb('.linksOptions > a').on('click', function(ele) {
        ele.preventDefault();
        lb(this.nextSibling.nextSibling).toggleClass('show');
    });

});