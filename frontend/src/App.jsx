import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom';
import { AuthProvider } from './contexts/AuthContext';
import Login from './components/auth/Login';
import Register from './components/auth/Register';
import Dashboard from './pages/Dashboard';
import SnippetForm from './pages/SnippetForm';
import ViewSnippet from './pages/ViewSnippet';
import PrivateRoute from './components/common/PrivateRoute';
import './styles/global.css';

const App = () => {
  return (
    <Router>
      <AuthProvider>
        <Routes>
          {/* Auth Routes */}
          <Route path="/login" element={<Login />} />
          <Route path="/register" element={<Register />} />
          
          {/* Protected Routes */}
          <Route 
            path="/dashboard" 
            element={
              <PrivateRoute>
                <Dashboard />
              </PrivateRoute>
            } 
          />
          
          <Route 
            path="/snippets/create" 
            element={
              <PrivateRoute>
                <SnippetForm />
              </PrivateRoute>
            } 
          />
          
          <Route 
            path="/snippets/edit/:id" 
            element={
              <PrivateRoute>
                <SnippetForm />
              </PrivateRoute>
            } 
          />
          
          <Route 
            path="/snippets/:id" 
            element={
              <PrivateRoute>
                <ViewSnippet />
              </PrivateRoute>
            } 
          />
          
          {/* Redirect to dashboard if authenticated, otherwise to login */}
          <Route path="/" element={<Navigate to="/dashboard" />} />
        </Routes>
      </AuthProvider>
    </Router>
  );
};

export default App;