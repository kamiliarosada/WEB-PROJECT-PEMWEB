public class PohonClan{
    public static void preOrder(Node root) {
        if (root != null) {
            System.out.println("Clan " + root.idntitas);
            preOrder(root.left);
            preOrder(root.right);
        }
    }
    public static void inOrder(Node root) {
        if (root == null) return;
        inOrder(root.left);
        System.out.println("Clan " + root.idntitas);
        inOrder(root.right);
    }
    public static void postOrder(Node root) {
        if (root == null) return;
        postOrder(root.left);
        postOrder(root.right);
        System.out.println("Clan " + root.idntitas);
    }
    public static void levelOrder(Node root) {
        if (root == null) {
            return;
        }
        Node[] queue = new Node[100];
        int front = 0, rear = 0;
        queue[rear++] = root;
        while (front < rear) {
            Node current = queue[front++];
            System.out.println("Clan " + current.idntitas + ": " + current.nilai);
            if (current.left != null) {
                queue[rear++] = current.left;
            }
            if (current.right != null) {
                queue[rear++] = current.right;
            }
        }
    }
    public static int findDepth(Node root, int clanId) {
        return findDepthClan(root, clanId, 0);
    }
    private static int findDepthClan(Node root, int clanId, int depth) {
        if (root == null) {
            return -1;
        }
        if (root.idntitas == clanId) {
            return depth;
        }
        int leftDepth = findDepthClan(root.left, clanId, depth + 1);
        if (leftDepth != -1) {
            return leftDepth;
        }
        return findDepthClan(root.right, clanId, depth + 1);
    }
    public static void printClanDepth(Node root, int clanId) {
        System.out.println("Mencari Kedalaman Clan " + clanId);
        int depth = findDepth(root, clanId);
        if (depth != -1) {
            System.out.println("Kedalaman Clan " + clanId + ": " + depth);
        } else {
            System.out.println("Clan " + clanId + " tidak ditemukan.");
        }
        System.out.println();
    }
}
