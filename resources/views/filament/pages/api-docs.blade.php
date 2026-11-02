<x-filament-panels::page>
    <div class="nc-api">
        <p class="nc-api-intro">{!! __('admin.api.intro', ['url' => $this->baseUrl]) !!}</p>

        {{-- General rules --}}
        <div class="nc-api-card">
            <h3 class="nc-api-card-title">{{ __('admin.api.rules_title') }}</h3>
            <ul class="nc-api-rules">
                @foreach (__('admin.api.rules') as $rule)
                    <li>{{ $rule }}</li>
                @endforeach
            </ul>
        </div>

        {{-- Endpoints --}}
        @foreach ($this->endpoints as $endpoint)
            @php($meta = __('admin.api.'.$endpoint['id']))
            @php($authKey = $endpoint['auth'] ?: 'none')

            <section class="nc-api-card">
                <header class="nc-api-head">
                    <span class="nc-api-method nc-api-method-{{ $endpoint['method'] }}">{{ $endpoint['method'] }}</span>
                    <code class="nc-api-path">{{ $this->baseUrl }}{{ $endpoint['path'] }}</code>
                    <span class="nc-api-auth nc-api-auth-{{ $authKey }}">{{ __('admin.api.auth.'.$authKey) }}</span>
                </header>

                <h3 class="nc-api-card-title">{{ $meta['title'] }}</h3>
                <p class="nc-api-desc">{{ $meta['desc'] }}</p>

                @if (! empty($meta['params']))
                    <div class="nc-api-table-scroll">
                        <table class="nc-api-table">
                            <thead>
                                <tr>
                                    <th>{{ __('admin.api.table.name') }}</th>
                                    <th>{{ __('admin.api.table.type') }}</th>
                                    <th>{{ __('admin.api.table.required') }}</th>
                                    <th>{{ __('admin.api.table.desc') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($meta['params'] as $param)
                                    <tr>
                                        <td><code>{{ $param[0] }}</code></td>
                                        <td>{{ $param[1] }}</td>
                                        <td>
                                            @if ($param[2])
                                                <span class="nc-api-req">{{ __('admin.api.table.yes') }}</span>
                                            @else
                                                <span class="nc-api-opt">{{ __('admin.api.table.no') }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $param[3] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="nc-api-noparams">{{ __('admin.api.no_params') }}</p>
                @endif

                @if ($response = $this->responseFor($endpoint['id']))
                    <h4 class="nc-api-sub">{{ __('admin.api.response_title') }}</h4>
                    <pre class="nc-api-pre"><code>{{ $response }}</code></pre>
                @endif

                @if (! empty($meta['notes']))
                    <p class="nc-api-notes">{{ $meta['notes'] }}</p>
                @endif
            </section>
        @endforeach
    </div>
</x-filament-panels::page>
