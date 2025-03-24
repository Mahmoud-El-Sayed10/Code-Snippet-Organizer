import { useState, useEffect } from "react";
import { useParams, useNavigate } from "react-router-dom";
import { Prism as SyntaxHighlighter } from "react-syntax-highlighter";
import { vscDarkPlus } from "react-syntax-highlighter/dist/esm/styles/prism";
import api from "../services/api";
import "../styles/ViewSnippet.css";

const ViewSnippet = () => {
  const { id } = useParams();
  const navigate = useNavigate();
  const [snippet, setSnippet] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [isFavorite, setIsFavorite] = useState(false);

  useEffect(() => {
    const fetchSnippet = async () => {
      try {
        const response = await api.get(`/snippets/${id}`);
        if (response.data.success) {
          setSnippet(response.data.data);
          setIsFavorite(response.data.is_favorited);
        } else {
          setError("Failed to load snippet");
        }
      } catch (err) {
        setError(
          "Error loading snippet: " +
            (err.response?.data?.message || err.message)
        );
      } finally {
        setLoading(false);
      }
    };

    fetchSnippet();
  }, [id]);

  const handleEdit = () => {
    navigate(`/snippets/edit/${id}`);
  };

  const handleDelete = async () => {
    if (window.confirm("Are you sure you want to delete this snippet?")) {
      try {
        const response = await api.delete(`/snippets/${id}`);
        if (response.data.success) {
          navigate("/dashboard");
        } else {
          setError("Failed to delete snippet");
        }
      } catch (err) {
        setError(
          "Error deleting snippet: " +
            (err.response?.data?.message || err.message)
        );
      }
    }
  };

  const toggleFavorite = async () => {
    try {
      let response;
      if (isFavorite) {
        response = await api.delete(`/favorites/${id}`);
      } else {
        response = await api.post(`/favorites/${id}`);
      }

      if (response.data.success) {
        setIsFavorite(!isFavorite);
      }
    } catch (err) {
      setError(
        "Error updating favorites: " +
          (err.response?.data?.message || err.message)
      );
    }
  };

  if (loading) {
    return <div className="loading">Loading snippet...</div>;
  }

  if (error) {
    return <div className="error-message">{error}</div>;
  }

  if (!snippet) {
    return <div className="not-found">Snippet not found</div>;
  }

  const language = snippet.language?.alias || "text";

  return (
    <div className="view-snippet-container">
      <div className="snippet-header">
        <div className="snippet-title-area">
          <h2>{snippet.title}</h2>
          <span className="language-badge">{snippet.language?.name}</span>
        </div>

        <div className="snippet-actions">
          <button
            className={`favorite-button ${isFavorite ? "favorited" : ""}`}
            onClick={toggleFavorite}
          >
            {isFavorite ? "Remove from Favorites" : "Add to Favorites"}
          </button>
          <button className="edit-button" onClick={handleEdit}>
            Edit
          </button>
          <button className="delete-button" onClick={handleDelete}>
            Delete
          </button>
        </div>
      </div>

      {snippet.description && (
        <div className="snippet-description">
          <p>{snippet.description}</p>
        </div>
      )}

      <div className="code-container">
        <SyntaxHighlighter
          language={language}
          style={vscDarkPlus}
          showLineNumbers={true}
          wrapLines={true}
        >
          {snippet.code_content}
        </SyntaxHighlighter>
      </div>

      {snippet.tags && snippet.tags.length > 0 && (
        <div className="snippet-tags">
          <h4>Tags:</h4>
          <div className="tags-list">
            {snippet.tags.map((tag) => (
              <span key={tag.id} className="tag-badge">
                {tag.name}
              </span>
            ))}
          </div>
        </div>
      )}

      <div className="back-link">
        <button onClick={() => navigate("/dashboard")} className="back-button">
          Back to Dashboard
        </button>
      </div>
    </div>
  );
};

export default ViewSnippet;
