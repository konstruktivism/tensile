<!-- resources/views/mail/base.blade.php -->
<mjml lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <mj-head>
        <!-- Include Google Web Font Figtree -->
        <mj-font name="Figtree" href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700;800;900&display=swap" />

        <mj-attributes>
            <mj-all font-family="Figtree,-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif" />
            <mj-text font-size="16px" />
            <mj-button background-color="#6D23CE" color="white" border-radius="8px" font-size="16px" font-weight="600" padding="10px 20px" />
        </mj-attributes>
        <mj-style inline="inline">
            /*
            Original Logo size
            width: 450px;
            height: 100px;
            */

            :root {
            --primary-color: #6D23CE;
            --primary-dark-color: #470e95;
            }

            .container {
            //background-image: url('{{ asset('images/bg-mail.png') }}') !important;
            }

            .container-inner {
            padding-top: 12px !important;
            border-left: 1px solid #dadada;
            border-right: 1px solid #dadada;
            background-color: #ffffff;
            }

            .container-header {
            border: 1px solid #dadada;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
            background-color: #ffffff;
            }

            .container-footer {
            border: 1px solid #dadada;
            border-bottom-left-radius: 8px;
            border-bottom-right-radius: 8px;
            background-color: #ffffff;
            }

            .container-nofooter {
            border-top: none;
            border-left: 1px solid #dadada;
            border-right: 1px solid #dadada;
            border-bottom: 1px solid #dadada;
            border-bottom-left-radius: 8px;
            border-bottom-right-radius: 8px;
            background-color: #ffffff;
            }

            .btn {
            padding-bottom: 24px !important;
            padding-top: 24px !important;
            }

            .body-text div strong {
            color: var(--primary-color) !important;
            }

            .btn-alt div a {
            background: #ffff !important;
            border: 1px solid var(--primary-color) !important;
            }

            .challenge div div {
            border: 1px solid #dadada;
            }

            .challenge div div div {
            border: none;
            }

            .challenge div strong {
            color: var(--primary-color) !important;
            }

            @media only screen and (max-width:480px) {
            .logo img {
            width: 90px !important; /* 450 / 5 = 90 */
            height: 20px !important; /* 100 / 5 = 20 */
            }

            .logo table {
            width: 100% !important;
            }

            .logo table  tr td {
            text-align: center !important;
            }

            .logo table  tr td a {
            display: inline-block;
            }

            .logo {
            text-align: center !important;
            }

            .container-header table tr td {
            padding: 6px 12px !important;
            }

            .container-footer table tr td {
            padding: 6px 12px 12px 12px !important;
            }

            .container-inner table tr td {
            padding: 12px 12px !important;
            }

            .container-inner table tr td table tr td table tr td {
            padding: 0 !important;
            }

            .footer_image table tbody tr td table {
            width: 100% !important;
            }

            .footer_image table tbody tr td table tbody tr td {
            text-align: center !important;
            width: 100% !important;
            }

            .footer_image img {
            width: 50px !important;
            display: inline-block !important;
            }

            .section-callout table tr td {
            padding: 0 0 6px 0 !important;
            }

            .colophon table tr td {
            padding: 12px !important;
            }

            .links a {
            text-align: center !important;
            width: 100% !important;
            display: block !important;
            }

            .container-footer .footer_image td {
            padding: 6px 3px !important;
            }

            .btn-columns {
            padding: 0 !important;
            }

            .btn-columns table tr td.btn {
            padding: 0 0 24px 0 !important;
            }

            .btn table {
            width: 100% !important;
            }

            .btn table tr td {
            padding: 3px 0px !important;
            }

            .col-image {
            display: none !important;
            }

            .challenge div div div {
            padding: 0 !important;
            }

            .challenge div div {
            padding: 0 !important;
            }

            .challenge img {
            width: 67px !important;
            height: 32px !important;
            padding-left: 12px !important;
            padding-top: 12px !important;
            }

            .challenge table tr td div {
            padding: 12px 12px 6px 12px !important;
            font-size: 12px !important;
            width: auto !important;
            }
            }
        </mj-style>
    </mj-head>
    <mj-body background-color="#fff" css-class="container" background-size="cover" background-position="top center" margin="24px">
        <mj-section padding="24px" css-class="container-header">
            <mj-column>
                <mj-image src="{{ asset('images/fynders-logo.png') }}"
                          alt="{{ config('app.name') }}"
                          height="25px"
                          width="112px"
                          align="left"
                          css-class="logo"
                          href="{{ config('app.url') }}"
                />
            </mj-column>
        </mj-section>

        <mj-section padding="12px 24px 24px 24px" css-class="container-inner">
            <mj-column>
                @hasSection('header_image')
                    <mj-image src="@yield('header_image')" alt="@hasSection('header') @yield('header') @endif" padding="0 24px 24px 24px" />
                @endif

                <mj-text font-size="24px" font-weight="600" letter-spacing="-.5px" color="#6D23CE">
                    @yield('header')
                </mj-text>

                <mj-text font-size="16px" line-height="150%" color="#777">
                    @yield('body')
                </mj-text>

                @hasSection('challenge')
                    <mj-section css-class="challenge" padding="12px 12px 0 12px" font-size="16px" line-height="150%" color="#777">
                        <mj-column>
                            <mj-image src="{{ asset('images/badge-challenge.png') }}" alt="Challenge" height="64px" padding="24px 24px 12px 24px" width="134px" align="left" />

                            <mj-text padding="0 24px 24px 24px">
                                @yield('challenge')
                            </mj-text>
                        </mj-column>
                    </mj-section>
                @endif

                @hasSection('button_url_alt')
                    <mj-section padding="0 2px" css-class="btn-columns">
                        <mj-column>
                            @hasSection('button_url')
                                <mj-button href="@yield('button_url')" css-class="btn" width="100%">
                                    @yield('button_text')
                                </mj-button>
                            @endif
                        </mj-column>

                        <mj-column>
                            <mj-button href="@yield('button_url_alt')" background-color="white" color="#6D23CE" border="1px solid #6D23CE;" css-class="btn btn-alt" width="100%">
                                @yield('button_text_alt')
                            </mj-button>
                        </mj-column>
                    </mj-section>
                @else
                    @hasSection('button_url')
                        <mj-button href="@yield('button_url')" css-class="btn">
                            @yield('button_text')
                        </mj-button>
                    @endif
                @endif

                @hasSection('callout')
                    @hasSection('callout_title')
                        <mj-text css-class="header">
                            @yield('callout_title')
                        </mj-text>
                    @endif

                    @php
                        $calloutContent = trim(View::yieldContent('callout'));
                        $calloutItems = json_decode($calloutContent, true);
                    @endphp

                    @if(is_array($calloutItems))
                        @foreach($calloutItems as $item)
                            <mj-section padding="8px 0" margin="0" css-class="section-callout">
                                <mj-column width="25%" css-class="col-image">
                                    <mj-text css-class="body-callout" align="center" background="#f6eeff" padding="24px" color="var(--primary-color)" border-radius="8px" line-height="150%" font-size="32px" font-weight="bold">
                                        {{ $loop->iteration }}
                                    </mj-text>
                                </mj-column>
                                <mj-column width="75%">
                                    <mj-text font-size="16px" line-height="150%" color="#777">
                                        {!! $item !!}
                                    </mj-text>
                                </mj-column>
                            </mj-section>
                        @endforeach
                    @endif
                @endif

                @hasSection('cta_bottom')
                    <mj-image src="{{ asset('images/intro-fynders.png') }}" alt="Challenge" height="206px" padding="24px 24px 12px 24px" width="157px" align="center" />

                    <mj-text font-size="16px" line-height="150%" color="#777" padding="12px 24px 0 24px">
                        @yield('cta_bottom')
                    </mj-text>

                    <mj-button href="{{ config('app.url') }}"  background-color="white" color="#6D23CE" border="1px solid #6D23CE;" css-class="btn btn-alt">
                        www.fynders.nl
                    </mj-button>
                @endif

                <mj-text css-class="body-text-bottom" padding="12px 24px 0 24px" align="center" color="#ADADAD" line-height="150%">
                    @yield('body_bottom')
                </mj-text>
            </mj-column>
        </mj-section>

        @hasSection('footer')
            <mj-section padding="24px 0" css-class="container-footer">
                <mj-column width="50px" padding="0" margin="0" css-class="footer_image">
                    <mj-image src="@yield('footer_image')"
                              alt="Footer Image"
                              height="50px"
                              width="50px"
                              padding-left="0"
                              padding-right="0"
                    />
                </mj-column>
                <mj-column width="75%">
                    <mj-text css-class="footer" color="#777" line-height="150%" align="left">
                        @yield('footer')
                    </mj-text>
                </mj-column>
            </mj-section>
        @else
            <mj-section padding="0 0 8px 0" css-class="container-nofooter">
                <mj-column padding="0" margin="0" css-class="footer_image">
                </mj-column>
            </mj-section>
        @endif

        <mj-section padding="24px" css-class="colophon">
            <mj-column>
                <mj-text font-weight="bold" css-class="links">
                    <a href="{{ config('app.url') }}"
                       color="#470e95"
                       font-weight="bold"
                       style="text-decoration: none; color: #6D23CE;">
                        {{ config('app.name') }}
                    </a>
                </mj-text>
            </mj-column>

            <mj-column>
                <mj-text font-size="14px" css-class="links">
                    <a href="{{ config('app.url') }}"
                       color="#777777"
                       font-weight="bold"
                       style="text-decoration: none; color: #999999;">
                        {{ __('LinkedIn') }}
                    </a>
                </mj-text>
            </mj-column>

            <mj-column>
                <mj-text font-size="14px" css-class="links">
                    <a href="{{ config('app.url') }}"
                       color="#777777"
                       font-weight="bold"
                       style="text-decoration: none; color: #999999;">
                        {{ __('Instagram') }}
                    </a>
                </mj-text>
            </mj-column>

            <mj-column>
                <mj-text font-size="14px" css-class="links">
                    <a href="mailto:info@fynders.nl"
                       color="#777777"
                       font-weight="bold"
                       style="text-decoration: none; color: #999999;">
                        {{ __('E-mail') }}
                    </a>
                </mj-text>
            </mj-column>
        </mj-section>
    </mj-body>
</mjml>
