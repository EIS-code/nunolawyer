<script type="text/javascript">
    document.addEventListener('readystatechange', () => {    
        if (document.readyState == 'complete') {
            $(document).on("click", "#status-to-follow", function() {
                $(".div-to-follow").removeClass("d-none");
            });

            $(document).on("click", "#status-default", function() {
                $(".div-to-follow").addClass("d-none");
            });

            $(document).on("click", "#status-work-done-all", function() {
                $(".div-to-follow").addClass("d-none");
            });

            // Purpose & Articles.
            $(document).on("click", "#plus-pa", function() {
                let clone     = $("#main-pa").clone()
                                             .prop("id", "")
                                             .find("#plus-pa").prop("id", "minus-pa").end()
                                             .find("#minus-pa").toggleClass("fa-plus").addClass("fa-trash").end()
                                             .find("input").val("").end()
                                             .find("textarea").val("").end(),
                    clonnedPa = $("#cloned-pa");

                if (clonnedPa) {
                    clonnedPa.before(clone);

                    numberingPa();
                }
            });
            $(document).on("click", "#minus-pa", function() {
                let self = $(this),
                    div  = self.closest("div").get(0);

                if (div) {
                    div.remove();

                    numberingPa();
                }
            });
            function numberingPa() {
                let div    = $("#row-pa"),
                    tables = div.find("table");

                if (tables.length > 0) {
                    var inc = 1;
                    tables.each(function() {
                        let self = $(this),
                            td   = self.find("td:first-child");

                        if (td) {
                            td.html(inc);
                            inc++;
                        }
                    });
                }
            }

            // Client Fees
            $(document).on("click", "#plus-cf", function() {
                let clone     = $("#main-cf").clone()
                                             .prop("id", "")
                                             .find("#plus-cf").prop("id", "minus-cf").end()
                                             .find("#minus-cf").toggleClass("fa-plus").addClass("fa-trash").end()
                                             .find("input").val("").end()
                                             .find("textarea").val("").end(),
                    clonnedCf = $("#cloned-cf");

                if (clonnedCf) {
                    clonnedCf.before(clone);

                    numberingCf();
                }
            });
            $(document).on("click", "#minus-cf", function() {
                let self = $(this),
                    div  = self.closest("div").get(0);

                if (div) {
                    div.remove();

                    numberingCf();
                }
            });
            function numberingCf() {
                let div    = $("#row-cf"),
                    tables = div.find("table");

                if (tables.length > 0) {
                    var inc = 1;
                    tables.each(function() {
                        let self = $(this),
                            td   = self.find("td:first-child:first");

                        if (td) {
                            td.html(inc);
                            inc++;
                        }
                    });
                }
            }

            // Progress Reports.
            $(document).on("click", "#plus-pr", function() {
                let clone     = $("#main-pr").clone()
                                             .prop("id", "")
                                             .find("#plus-pr").prop("id", "minus-pr").end()
                                             .find("#minus-pr").toggleClass("fa-plus").addClass("fa-trash").end()
                                             .find("input").val("").end()
                                             .find("textarea").val("").end()
                                             .find("a").remove().end()
                                             .find("br").remove().end(),
                    clonnedPr = $("#cloned-pr");

                if (clonnedPr) {
                    clonnedPr.before(clone);

                    numberingPr();
                }
            });
            $(document).on("click", "#minus-pr", function() {
                let self = $(this),
                    div  = self.closest("div").get(0);

                if (div) {
                    div.remove();

                    numberingPr();
                }
            });
            function numberingPr() {
                let div    = $("#row-pr"),
                    tables = div.find("table");

                if (tables.length > 0) {
                    var inc = 1;
                    tables.each(function() {
                        let self = $(this),
                            td   = self.find("td:first-child");

                        if (td) {
                            td.html(inc);
                            inc++;
                        }
                    });
                }
            }

            // Client private informations.
            $(document).on("click", "#plus-ci", function() {
                let clone     = $("#main-ci").clone()
                                             .prop("id", "")
                                             .find("#plus-ci").prop("id", "minus-ci").end()
                                             .find("#minus-ci").toggleClass("fa-plus").addClass("fa-trash").end()
                                             .find("input").val("").end()
                                             .find("textarea").val("").end(),
                    clonnedCi = $("#cloned-ci");

                if (clonnedCi) {
                    clonnedCi.before(clone);

                    numberingCi();
                }
            });
            $(document).on("click", "#minus-ci", function() {
                let self = $(this),
                    div  = self.closest("div").get(0);

                if (div) {
                    div.remove();

                    numberingCi();
                }
            });
            function numberingCi() {
                let div    = $("#row-ci"),
                    tables = div.find("table");

                if (tables.length > 0) {
                    var inc = 1;
                    tables.each(function() {
                        let self = $(this),
                            td   = self.find("td:first-child");

                        if (td) {
                            td.html(inc);
                            inc++;
                        }
                    });
                }
            }

            // Client documents.
            $(document).on("click", "#plus-cd", function() {
                let clone     = $("#main-cd").clone()
                                             .prop("id", "")
                                             .find("#plus-cd").prop("id", "minus-cd").end()
                                             .find("#minus-cd").toggleClass("fa-plus").addClass("fa-trash").end()
                                             .find("input").val("").end()
                                             .find("textarea").val("").end()
                                             .find("a").remove().end()
                                             .find("br").remove().end(),
                    clonnedCd = $("#cloned-cd");

                if (clonnedCd) {
                    clonnedCd.before(clone);

                    numberingCd();
                }
            });
            $(document).on("click", "#minus-cd", function() {
                let self = $(this),
                    div  = self.closest("div").get(0);

                if (div) {
                    div.remove();

                    numberingCd();
                }
            });
            function numberingCd() {
                let div    = $("#row-cd"),
                    tables = div.find("table");

                if (tables.length > 0) {
                    var inc = 1;
                    tables.each(function() {
                        let self = $(this),
                            td   = self.find("td:first-child");

                        if (td) {
                            td.html(inc);
                            inc++;
                        }
                    });
                }
            }

            // Terms and conditions.
            $(document).on("click", "#plus-tc", function() {
                let clone     = $("#main-tc").clone()
                                             .prop("id", "")
                                             .find("#plus-tc").prop("id", "minus-tc").end()
                                             .find("#minus-tc").toggleClass("fa-plus").addClass("fa-trash").end()
                                             .find("input").val("").end()
                                             .find("textarea").val("").end(),
                    clonnedTc = $("#cloned-tc");

                if (clonnedTc) {
                    clonnedTc.before(clone);

                    numberingTc();
                }
            });
            $(document).on("click", "#minus-tc", function() {
                let self = $(this),
                    div  = self.closest("div").get(0);

                if (div) {
                    div.remove();

                    numberingTc();
                }
            });
            function numberingTc() {
                let div    = $("#row-tc"),
                    tables = div.find("table").not(':first');

                if (tables.length > 0) {
                    var inc = 1;
                    tables.each(function() {
                        let self = $(this),
                            td   = self.find("td:first-child");

                        if (td) {
                            td.html(inc);
                            inc++;
                        }
                    });
                }
            }

            // Assign
            $(document).on("click", "#plus-sas", function() {
                let clone     = $("#main-sas").clone()
                                             .prop("id", "")
                                             .find("#plus-sas").prop("id", "minus-sas").end()
                                             .find("#minus-sas").toggleClass("fa-plus").addClass("fa-trash").end()
                                             .find("input").val("").end()
                                             .find("textarea").val("").end(),
                    clonnedSas = $("#cloned-sas");

                if (clonnedSas) {
                    clonnedSas.before(clone);

                    numberingSas();
                }
            });
            $(document).on("click", "#minus-sas", function() {
                let self = $(this),
                    div  = self.closest("div").get(0);

                if (div) {
                    div.remove();

                    numberingSas();
                }
            });
            function numberingSas() {
                select2PurposeArticles();

                let div    = $("#row-sas"),
                    tables = div.find("table");

                if (tables.length > 0) {
                    var inc = 1;
                    tables.each(function() {
                        let self = $(this),
                            td   = self.find("td:first-child");

                        if (td) {
                            td.html(inc);
                            inc++;
                        }
                    });
                }
            }

            // Select2
            function select2PurposeArticles()
            {
                $(".purpose_articles").select2({
                    placeholder: "{{ __('Select purpose / articles') }}",
                    allowClear: true,
                    width: '100%'
                }).on("select2:select", function (e) {
                    $("#last_purpose_articles").val(e.params.data.id);

                    let $element = $(e.params.data.element);

                    if ($element) {
                        $element.detach();
                        $(this).append($element);
                        $(this).trigger("change");
                    }
                }).on("select2:unselect", function (e) {
                    $("#last_purpose_articles").val(e.params.data.id);
                });
            }
            select2PurposeArticles();

            $(".work_status").select2({
                placeholder: "{{ __('Select work status') }}",
                allowClear: true,
                width: '100%'
            });

            $(".assign_to").select2({
                placeholder: "{{ __('Select') }}",
                allowClear: true,
                width: '100%'
            });

            let togglePassword = $(".togglePassword");
            if (togglePassword) {
                togglePassword.on('click', function (e) {
                    let passwordInput = $(this).next();

                    if (passwordInput) {
                        if (passwordInput.attr('type') == 'password') {
                            passwordInput.attr('type', 'text');
                        } else {
                            passwordInput.attr('type', 'password');
                        }
                    }
                });
            }

            /*$("input[id='number']").on("keyup", function(e) {
                let value = $(this).val();

                // !/^[+-]?\d+$/
                // /^[\d\(\)\-+]+$/
                if (/^[\d\(\)\-+]+$/.test(value)) {
                    return true;
                }

                return false;
            });*/
        }
    });
    function checkNumber(event) {
        event = event || window.event;
        console.log(event.keyCode);

        var allowedKeys = [
            48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 8, 189, 9, 37, 39, 190, 13
        ];

        var isMinus = (event.target.value.indexOf("-") !== -1),
            isPlus  = (event.target.value.indexOf("+") !== -1);

        if (isMinus) {
            var index = allowedKeys.indexOf(187);
            allowedKeys.splice(index, 1);

            var index = allowedKeys.indexOf(189);
            allowedKeys.splice(index, 1);
        }
        if (isPlus) {
            var index = allowedKeys.indexOf(189);
            allowedKeys.splice(index, 1);

            var index = allowedKeys.indexOf(187);
            allowedKeys.splice(index, 1);
        }

        return (allowedKeys.indexOf(event.keyCode) !== -1 && event.key != '_');
    }
</script>
