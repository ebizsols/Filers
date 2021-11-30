<div class="card">
    <div class="card-header">
        <h3>{{ trans('zohobooks::general.total', ['type' => trans_choice('zohobooks::general.types.' . $type, 2), 'count' => $total]) }}</h3>
    </div>

    <div class="card-body">
        <el-progress :text-inside="true" :stroke-width="24" :percentage="progress.total" :status="progress.status"></el-progress>

        <div id="progress-text" class="mt-3" v-html="progress.text"></div>

        <!-- Double-Entry is check -->
        <div class="card-body d-none">
            <div id="dobule-entry-message">
                <a href="https://akaunting.com/tr/apps/double-entry?utm_source=Suggestion&utm_medium=App&utm_campaign=DoubleEntry&redirect={{ base64_encode(env('APP_URL')) }}" target="_blank" class="btn btn-success">
                    <span class="fa fa-shopping-cart"></span> &nbsp; {{ trans('zohobooks::general.buttons.buy') }}
                </a>

                <a class="btn btn-icon" @click="step()">
                    <span class="fa fa-play"></span> &nbsp; {{ trans('loyverse::general.buttons.continue') }}
                </a>
            </div>
        </div>
    </div>
</div>
