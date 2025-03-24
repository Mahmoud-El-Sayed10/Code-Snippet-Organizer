import { useNavigate } from "react-router-dom";
import "./SnippetCard.css";

const SnippetCard = ({ snippet }) => {
  const navigate = useNavigate();

  const handleCardClick = () => {
    navigate(`/snippets/${snippet.id}`);
  };

  const truncate = (text, maxLength = 100) => {
    if (text.length <= maxLength) return text;
    return text.substr(0, maxLength) + "...";
  };

  return (
    <div className="snippet-card" onClick={handleCardClick}>
      <div className="snippet-card-header">
        <h3 className="snippet-title">{snippet.title}</h3>
        <span className="language-badge">
          {snippet.language?.name || "Unknown"}
        </span>
      </div>

      <div className="snippet-preview">
        <pre>{truncate(snippet.code_content, 150)}</pre>
      </div>

      <div className="snippet-footer">
        <div className="snippet-tags">
          {snippet.tags?.map((tag) => (
            <span key={tag.id} className="tag-badge">
              {tag.name}
            </span>
          ))}
        </div>

        <div className="snippet-actions">
          <button
            className="edit-button"
            onClick={(e) => {
              e.stopPropagation();
              navigate(`/snippets/edit/${snippet.id}`);
            }}
          >
            Edit
          </button>
        </div>
      </div>
    </div>
  );
};

export default SnippetCard;
