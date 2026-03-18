<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SdgContribution;
use Gate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SdgMappingController extends Controller
{
    public function index(Request $request)
    {
        abort_if(Gate::denies('publication_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->ajax()) {
            return $this->getDataTableData($request);
        }

        return view('admin.sdg-mappings.index');
    }

    private function getDataTableData(Request $request)
    {
        $query = SdgContribution::query()
            ->selectRaw('sdg, COUNT(*) as total_contributions, MAX(COALESCE(submitted_at, created_at)) as latest_submission_at')
            ->whereNotNull('sdg')
            ->groupBy('sdg');

        if ($request->has('search') && !empty($request->search['value'])) {
            $search = trim($request->search['value']);
            if (is_numeric($search)) {
                $query->havingRaw('sdg = ?', [(int) $search]);
            }
        }

        $records = $query->get();

        $orderColumn = (int) $request->input('order.0.column', 0);
        $orderDir = strtolower($request->input('order.0.dir', 'asc')) === 'desc' ? 'desc' : 'asc';

        if ($orderColumn === 0) {
            $records = $orderDir === 'desc' ? $records->sortByDesc('sdg')->values() : $records->sortBy('sdg')->values();
        } elseif ($orderColumn === 1) {
            $records = $orderDir === 'desc' ? $records->sortByDesc('total_contributions')->values() : $records->sortBy('total_contributions')->values();
        } else {
            $records = $orderDir === 'desc' ? $records->sortByDesc('latest_submission_at')->values() : $records->sortBy('latest_submission_at')->values();
        }

        $recordsTotal = $records->count();

        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);
        $paged = $records->slice($start, $length > 0 ? $length : null)->values();

        $data = $paged->map(function ($item) {
            return [
                'sdg' => '<strong>SDG ' . (int) $item->sdg . '</strong>',
                'total_contributions' => '<span class="badge badge-info">' . (int) $item->total_contributions . '</span>',
                'latest_submission' => $item->latest_submission_at ? date('M d, Y', strtotime($item->latest_submission_at)) : 'N/A',
                'actions' => '<a class="btn btn-sm btn-outline-primary" href="' . route('admin.sdg-mappings.show', (int) $item->sdg) . '" title="View" aria-label="View"><span class="material-icons-outlined">visibility</span></a>',
            ];
        });

        return response()->json([
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
            'data' => $data,
        ]);
    }

    public function show($sdgMapping)
    {
        abort_if(Gate::denies('publication_read'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $sdgNumber = (int) $sdgMapping;

        $contributions = SdgContribution::with(['submitter', 'workflow'])
            ->where('sdg', $sdgNumber)
            ->orderByDesc('submitted_at')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.sdg-mappings.show', [
            'sdgNumber' => $sdgNumber,
            'contributions' => $contributions,
        ]);
    }
}
