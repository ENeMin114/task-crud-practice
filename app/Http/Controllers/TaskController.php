<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // 1. データベースからすべてのタスクを取得する
        $tasks = Task::all();

        // 2. 取得したデータをJSON形式でレスポンス（返却）する
        return response()->json($tasks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. 送られてきたデータのバリデーション（入力チェック）を行う
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'status'      => 'required|in:pending,in_progress,completed',
            'due_date'    => 'nullable|date',
        ]);

        // 【教材の補足】認証は実装しないため、仮のユーザーID（1）をセットします
        $validated['user_id'] = 1;

        // 2. データベースに新しいタスクを保存する
        $task = Task::create($validated);

        // 3. 作成されたデータを、ステータスコード「201（作成完了）」とともにJSONで返す
        return response()->json($task, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // 1. 指定されたIDのタスクを検索する（見つからない場合は404エラーを自動で返す）
        $task = Task::findOrFail($id);

        // 2. 見つかったタスクのデータをJSON形式で返却する
        return response()->json($task);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // 1. 更新したいタスクが本当に存在するか検索する
        $task = Task::findOrFail($id);

        // 2. 送られてきた更新データのバリデーション（入力チェック）を行う
        $validated = $request->validate([
            'title'       => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'status'      => 'sometimes|required|in:pending,in_progress,completed',
            'due_date'    => 'nullable|date',
        ]);

        // 3. データベースのデータを新しい内容で更新する
        $task->update($validated);

        // 4. 更新完了したタスクのデータをJSON形式で返却する
        return response()->json($task);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // 1. 削除したいタスクが本当に存在するか検索する（ない場合は自動で404エラー）
        $task = Task::findOrFail($id);

        // 2. データベースからこのタスクのレコードを削除する
        $task->delete();

        // 3. 削除完了を伝えるため、中身が空のレスポンスとステータスコード「204（データなし）」を返す
        return response()->json(null, 204);
    }
} // ⬅️ 💡ここにクラスを閉じる波括弧を追加しました！
