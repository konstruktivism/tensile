<!-- resources/views/mail/base.blade.php -->
<mjml lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <mj-head>
        <mj-attributes>
            <mj-all font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif" />
            <mj-text font-size="16px" />
            <mj-button background-color="#1b1b1b" color="white" border-radius="4px" font-size="16px" font-weight="600" padding="10px 20px" />
        </mj-attributes>
        <mj-style inline="inline">

        </mj-style>
    </mj-head>
    <mj-body css-class="container" background-size="cover" background-position="top center" margin="24px">
        <mj-section padding="24px" css-class="container-header">
            <mj-column>
                <mj-text font-size="24px" font-weight="600" letter-spacing="-.5px" color="#1b1b1b">
                    {{ strtolower(config('app.name')) }}
                </mj-text>

                <mj-divider border-width="1px" border-color="#e5e7eb" />
            </mj-column>
        </mj-section>

        <mj-section padding="12px 24px 24px 24px" css-class="container-inner">
            <mj-column>
                <mj-text font-size="18px" font-weight="600" letter-spacing="-.25px" color="#1b1b1b">
                    @yield('header')
                </mj-text>

                <mj-text font-size="15px" line-height="150%" color="#737373">
                    @yield('body')
                </mj-text>

                @hasSection('button_url')
                    <mj-button href="@yield('button_url')" align="left">
                        @yield('button_text')
                    </mj-button>
                @endif
            </mj-column>
        </mj-section>

        <mj-section padding="24px">
            <mj-column>
                <mj-divider border-width="1px" border-color="#e5e7eb" />

                <mj-text font-size="15px">
                    <a href="{{ config('app.url') }}"
                       style="text-decoration: none; color: #a5a5a5;">
                        {{ config('app.slogan') }}
                    </a>
                </mj-text>
            </mj-column>
        </mj-section>
    </mj-body>
</mjml>
