public class Node {
    int nilai;
    int idntitas;
    Node left, right;

    public Node(int idntitas, int nilai) {
        this.idntitas = idntitas;
        this.nilai = nilai;
        left = right = null;
    }

    public static void printPre(Fam don, int i) {
        throw new UnsupportedOperationException("Unimplemented method 'printPre'");
    }
}
