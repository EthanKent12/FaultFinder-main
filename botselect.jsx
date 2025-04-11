import { useState, useEffect } from "react";
import { Card } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Switch } from "@/components/ui/switch";
import { Table, TableHeader, TableRow, TableHead, TableBody, TableCell } from "@/components/ui/table";
import { PanelRightClose, MessageSquare } from "lucide-react";
import { motion } from "framer-motion";

export default function BotSelect() {
  const [darkMode, setDarkMode] = useState(false);
  const [errors, setErrors] = useState([]);
  const [transactions, setTransactions] = useState([]);
  const [selectedTransaction, setSelectedTransaction] = useState(null);
  const [newComment, setNewComment] = useState("");
  const [isCommentPanelOpen, setIsCommentPanelOpen] = useState(false);

  const metrics = {
    totalErrors: errors.length,
    successRate: (errors.filter((e) => e.status === "success").length / errors.length) * 100,
    warningRate: (errors.filter((e) => e.status === "warning").length / errors.length) * 100,
    errorRate: (errors.filter((e) => e.status === "error").length / errors.length) * 100,
  };

  useEffect(() => {
    // Fetch error data
    fetch("https://your-api-endpoint.com/errors")
      .then((response) => response.json())
      .then((data) => {
        setErrors(data.errors); // Assuming the API returns an object with 'errors' as an array
      })
      .catch((error) => {
        console.error("Error fetching errors:", error);
      });

    // Fetch transaction data
    fetch("https://your-api-endpoint.com/transactions")
      .then((response) => response.json())
      .then((data) => {
        setTransactions(data.transactions); // Assuming the API returns an object with 'transactions' as an array
      })
      .catch((error) => {
        console.error("Error fetching transactions:", error);
      });
  }, []);

  const addComment = () => {
    if (newComment.trim() && selectedTransaction) {
      setTransactions(
        transactions.map((trans) =>
          trans.id === selectedTransaction.id ? { ...trans, comments: [...trans.comments, newComment] } : trans
        )
      );
      setNewComment("");
    }
  };

  return (
    <div className={darkMode ? "dark bg-gray-900 text-white" : "bg-white text-black"}>
      <div className="p-6 flex justify-between items-center">
        <h1 className="text-2xl font-bold">Fault Finder Dashboard</h1>
        <Button
          onClick={(any) => setDarkMode(!darkMode)}
          className="border p-2 rounded-lg bg-blue-500 text-white"
        >
          {darkMode ? "Switch to Light Mode" : "Switch to Dark Mode"}
        </Button>
      </div>

      {/* Metrics Section */}
      <div className="grid grid-cols-4 gap-4 p-6">
        {Object.entries(metrics).map(([key, value]) => (
          <Card key={key} className="p-4">
            <h2 className="text-lg font-semibold">{key.replace(/([A-Z])/g, " $1").trim()}</h2>
            <p className="text-xl">
              {value.toFixed(2)}{key.includes("Rate") ? "%" : ""}
            </p>
          </Card>
        ))}
      </div>

      {/* Transaction Table */}
      <div className="p-6">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Transaction ID</TableHead>
              <TableHead>Date</TableHead>
              <TableHead>Amount</TableHead>
              <TableHead>Status</TableHead>
              <TableHead>Actions</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {transactions.map((trans) => (
              <TableRow key={trans.id}>
                <TableCell>{trans.id}</TableCell>
                <TableCell>{trans.date}</TableCell>
                <TableCell>{`$${trans.amount}`}</TableCell>
                <TableCell
                  className={
                    trans.status === "success"
                      ? "text-green-500"
                      : trans.status === "warning"
                      ? "text-yellow-500"
                      : "text-red-500"
                  }
                >
                  {trans.status}
                </TableCell>
                <TableCell>
                  <Button
                    size="sm"
                    onClick={(any) => {
                      setSelectedTransaction(trans);
                      setIsCommentPanelOpen(true);
                    }}
                  >
                    <MessageSquare size={16} />
                  </Button>
                </TableCell>
              </TableRow>
            ))}
          </TableBody>
        </Table>
      </div>

      {/* Comment Panel */}
      {isCommentPanelOpen && selectedTransaction && (
        <motion.div
          className="fixed top-0 right-0 w-80 h-full bg-gray-100 dark:bg-gray-800 p-4 shadow-lg"
          animate={{ x: 0 }}
          initial={{ x: 300 }}
        >
          <div className="flex justify-between">
            <h2 className="text-lg font-bold">Comments for {selectedTransaction.id}</h2>
            <Button
              variant="ghost"
              onClick={(any) => setIsCommentPanelOpen(false)}
              className="bg-red-500 text-white"
            >
              <PanelRightClose size={20} />
            </Button>
          </div>
          <div className="mt-4">
            {selectedTransaction.comments.length > 0 ? (
              selectedTransaction.comments.map((comment, index) => (
                <div key={index} className="p-2 bg-gray-200 dark:bg-gray-700 rounded-md mb-2">
                  {comment}
                </div>
              ))
            ) : (
              <p>No comments yet</p>
            )}
          </div>
          <div className="mt-4">
            <input
              type="text"
              placeholder="Add a comment..."
              value={newComment}
              onChange={(e) => setNewComment(e.target.value)}
              className="w-full p-2 border rounded-md"
            />
            <Button className="mt-2 w-full" onClick={addComment} disabled={!newComment.trim()}>
              Add Comment
            </Button>
          </div>
        </motion.div>
      )}
    </div>
  );
}
